<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\CraftingMaterialCost;
	use App\Entity\Slot;
	use App\Entity\Weapon;
	use App\Entity\WeaponCraftingInfo;
	use App\Game\Attribute;
	use App\Game\WeaponType;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\KiranicoConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\ScraperInterface;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponHelper;
	use App\Scraping\Type;
	use App\Utility\StringUtil;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoWeaponsScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		private const PATHS = [
			WeaponType::GREAT_SWORD => '/great-sword',
			WeaponType::LONG_SWORD => '/long-sword',
			WeaponType::SWORD_AND_SHIELD => '/sword',
			WeaponType::DUAL_BLADES => '/dual-blades',
			WeaponType::HAMMER => '/hammer',
			WeaponType::HUNTING_HORN => '/hunting-horn',
			WeaponType::LANCE => '/lance',
			WeaponType::GUNLANCE => '/gunlance',
			WeaponType::SWITCH_AXE => '/switch-axe',
			WeaponType::CHARGE_BLADE => '/charge-blade',
			WeaponType::INSECT_GLAIVE => '/insect-glaive',
			WeaponType::LIGHT_BOWGUN => '/light-bowgun',
			WeaponType::HEAVY_BOWGUN => '/heavy-bowgun',
			WeaponType::BOW => '/bow',
		];

		private const SLOT_KEYS = [
			Attribute::SLOT_RANK_1,
			Attribute::SLOT_RANK_2,
			Attribute::SLOT_RANK_3,
		];

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * @var Weapon[]
		 */
		protected $weaponCache = [];

		/**
		 * KiranicoWeaponsScraper constructor.
		 *
		 * @param KiranicoConfiguration $configuration
		 * @param ObjectManager         $manager
		 */
		public function __construct(KiranicoConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::WEAPONS);

			$this->manager = $manager;
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(array $context = []): void {
			$subtypes = $context[ScraperInterface::CONTEXT_SUBTYPES] ?? [];

			$this->progressBar->append($subtypes ? sizeof($subtypes) : sizeof(self::PATHS));

			foreach (self::PATHS as $weaponType => $path) {
				if ($subtypes && !in_array($weaponType, $subtypes))
					continue;

				$uri = $this->configuration->getBaseUri()->withPath($path);
				$response = $this->getWithRetry($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$crawler = (new Crawler($response->getBody()->getContents()))
					->filter('.container table tr td:first-child a');
				$count = $crawler->count();

				$this->progressBar->append($count);

				for ($i = 0; $i < $count; $i++) {
					$node = $crawler->eq($i);

					$this->process(parse_url($node->attr('href'), PHP_URL_PATH), $weaponType);

					$this->progressBar->advance();
				}

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param string $path
		 * @param string $weaponType
		 *
		 * @return void
		 */
		protected function process(string $path, string $weaponType): void {
			$uri = $this->configuration->getBaseUri()->withPath($path);
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$sections = (new Crawler($response->getBody()->getContents()))->filter('.container .col-lg-9.px-2 .card');
			$data = KiranicoWeaponHelper::parseSectionData($sections, $weaponType);

			$weapon = $this->getWeapon($data->getName());

			if (!$weapon) {
				$weapon = new Weapon($data->getName(), $weaponType, $data->getRarity());

				$this->manager->persist($weapon);
				$this->weaponCache[$data->getName()] = $weapon;
			} else
				$weapon->setRarity($data->getRarity());

			$weapon->setAttributes($data->getAttributes());

			$weapon->getSharpness()->import($data->getSharpness());

			$weapon->getSlots()->clear();

			foreach (self::SLOT_KEYS as $slotKey) {
				$count = $weapon->getAttribute($slotKey);

				if (!$count)
					continue;

				$rank = (int)substr($slotKey, -1);

				for ($i = 0; $i < $count; $i++)
					$weapon->getSlots()->add(new Slot($rank));
			}

			$info = $weapon->getCrafting();

			if (!$info) {
				$info = new WeaponCraftingInfo($data->isCraftable());

				$weapon->setCrafting($info);
			} else
				$info->setCraftable($data->isCraftable());

			if ($data->getCraftingPrevious()) {
				$previous = $this->getWeapon($data->getCraftingPrevious());

				if (!$previous)
					throw new \RuntimeException('Could not find previous weapon named ' .
						$data->getCraftingPrevious());
				else if (!$previous->getCrafting())
					throw new \RuntimeException('Could not find crafting info for previous named ' .
						$previous->getName());

				$prevInfo = $previous->getCrafting();

				if (!$prevInfo->getBranches()->contains($weapon))
					$prevInfo->getBranches()->add($weapon);

				$info->setPrevious($previous);
			}

			$info->getCraftingMaterials()->clear();
			$info->getUpgradeMaterials()->clear();

			if ($crafting = $data->getCraftingMaterials()) {
				/**
				 * @var string $itemName
				 * @var int    $amount
				 */
				foreach ($crafting as $itemName => $amount) {
					$item = $this->manager->getRepository('App:Item')->findOneBy([
						'name' => $itemName,
					]);

					if (!$item)
						throw new \RuntimeException('Could not find item named ' . $itemName);

					$info->getCraftingMaterials()->add(new CraftingMaterialCost($item, $amount));
				}
			}

			if ($upgrading = $data->getUpgradeMaterials()) {
				/**
				 * @var string $itemName
				 * @var int    $amount
				 */
				foreach ($upgrading as $itemName => $amount) {
					$item = $this->manager->getRepository('App:Item')->findOneBy([
						'name' => $itemName,
					]);

					if (!$item)
						throw new \RuntimeException('Could not find item named ' . $itemName);

					$info->getUpgradeMaterials()->add(new CraftingMaterialCost($item, $amount));
				}
			}
		}

		/**
		 * @param string $name
		 *
		 * @return Weapon|null
		 */
		protected function getWeapon(string $name): ?Weapon {
			$name = StringUtil::replaceNumeralRank($name);

			if (isset($this->weaponCache[$name]))
				return $this->weaponCache[$name];

			return $this->manager->getRepository('App:Weapon')->findOneBy([
				'name' => $name,
			]);
		}
	}