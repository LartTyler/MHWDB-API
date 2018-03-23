<?php
	namespace App\Scraper\Kiranico\Scrapers;

	use App\Entity\CraftingMaterialCost;
	use App\Entity\Weapon;
	use App\Entity\WeaponCraftingInfo;
	use App\Game\Attribute;
	use App\Game\WeaponType;
	use App\Scraper\AbstractScraper;
	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use App\Scraper\Kiranico\Scrapers\Helpers\WeaponMainDataSection;
	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\ParserType;
	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponData;
	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponDataParserInterface;
	use App\Scraper\ScraperType;
	use App\Scraper\SubtypeAwareScraperInterface;
	use App\Utility\StringUtil;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\ORM\EntityManager;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoWeaponsScraper extends AbstractScraper implements SubtypeAwareScraperInterface {
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

		private const LAYOUTS = [
			'' => [
				ParserType::MAIN => 1,
				ParserType::UPGRADE => 2,
				ParserType::MATERIALS => 3,
			],
			WeaponType::LIGHT_BOWGUN => [
				ParserType::MAIN => 1,
				ParserType::AMMO => 2,
				ParserType::UPGRADE => 3,
				ParserType::MATERIALS => 4,
			],
			WeaponType::HEAVY_BOWGUN => [
				ParserType::MAIN => 1,
				ParserType::AMMO => 2,
				ParserType::UPGRADE => 3,
				ParserType::MATERIALS => 4,
			],
		];

		/**
		 * @var KiranicoScrapeTarget
		 */
		protected $target;

		/**
		 * @var EntityManager
		 */
		protected $manager;

		/**
		 * @var WeaponDataParserInterface[]
		 */
		protected $parsers;

		/**
		 * @var Weapon[]
		 */
		protected $weaponCache = [];

		/**
		 * KiranicoWeaponsScraper constructor.
		 *
		 * @param KiranicoScrapeTarget        $target
		 * @param RegistryInterface           $registry
		 * @param WeaponDataParserInterface[] $parsers MUST be passed in section index order
		 */
		public function __construct(KiranicoScrapeTarget $target, RegistryInterface $registry, array $parsers) {
			parent::__construct($target, ScraperType::WEAPONS);

			$this->manager = $registry->getManager();
			$this->parsers = $parsers;
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(array $subtypes = []): void {
			foreach (self::PATHS as $weaponType => $path) {
				if ($subtypes && !in_array($weaponType, $subtypes))
					continue;

				$uri = $this->target->getBaseUri()->withPath($path);
				$response = $this->target->getHttpClient()->get($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$crawler = (new Crawler($response->getBody()->getContents()))
					->filter('.container table tr td:first-child a');

				for ($i = 0, $ii = $crawler->count(); $i < $ii; $i++) {
					$node = $crawler->eq($i);

					$this->process(parse_url($node->attr('href'), PHP_URL_PATH), $weaponType);
				}

				// We sleep to avoid hitting the target too fast
				sleep(2);
			}
		}

		/**
		 * @param string $path
		 * @param string $weaponType
		 *
		 * @return void
		 * @throws \Http\Client\Exception
		 */
		protected function process(string $path, string $weaponType): void {
			$uri = $this->target->getBaseUri()->withPath($path);

			try {
				$result = $this->target->getHttpClient()->get($uri);
			} catch (\Exception $e) {
				// An exception here means we were hitting the target too hard (99% of the time at least)
				// Sleep and then try again
				sleep(3);

				$result = $this->target->getHttpClient()->get($uri);
			}

			if ($result->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$sections = (new Crawler($result->getBody()->getContents()))->filter('.container .col-lg-9.px-2 .card');

			$data = new WeaponData();

			if (isset(self::LAYOUTS[$weaponType]))
				$layout = self::LAYOUTS[$weaponType];
			else
				$layout = self::LAYOUTS[''];

			foreach ($this->parsers as $key => $parser) {
				if (!isset($layout[$key]))
					continue;

				$parser->parse($sections->eq($layout[$key]), $data);
			}

			$weapon = $this->getWeapon($data->getName());

			if (!$weapon) {
				$weapon = new Weapon($data->getName(), $weaponType, $data->getRarity());

				$this->manager->persist($weapon);
				$this->weaponCache[$data->getName()] = $weapon;
			} else
				$weapon->setRarity($data->getRarity());

			$weapon->setAttributes($data->getAttributes());

			$info = $weapon->getCrafting();

			if (!$info) {
				$info = new WeaponCraftingInfo($data->isCraftable());

				$weapon->setCrafting($info);
			} else
				$info->setCraftable($data->isCraftable());

			if ($data->getCraftingPrevious()) {
				$previous = $this->getWeapon($data->getCraftingPrevious());

				if (!$previous)
					throw new \RuntimeException('Could not find previous weapon named ' . $data->getCraftingPrevious());
				else if (!$previous->getCrafting())
					throw new \RuntimeException('Could not find crafting info for previous named ' . $previous->getName());

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