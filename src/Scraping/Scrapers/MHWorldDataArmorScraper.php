<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Armor;
	use App\Entity\ArmorCraftingInfo;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use App\Entity\ArmorSetBonusRank;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\Skill;
	use App\Entity\Slot;
	use App\Game\ArmorRank;
	use App\Game\ArmorType;
	use App\Game\Attribute;
	use App\Game\Element;
	use App\Game\Gender;
	use App\Scraping\Configurations\GithubConfiguration;
	use App\Scraping\Scrapers\Helpers\ArmorHelper;
	use App\Scraping\Scrapers\Helpers\CsvReader;
	use App\Scraping\Type;
	use Doctrine\Common\Persistence\ObjectManager;
	use Psr\Http\Message\UriInterface;
	use Symfony\Component\HttpFoundation\Response;

	class MHWorldDataArmorScraper extends AbstractMHWorldDataScraper {
		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * @var Armor[]
		 */
		protected $armorCache = [];

		/**
		 * @var ArmorSet[]
		 */
		protected $armorSetCache = [];

		/**
		 * MHWorldDataArmorScraper constructor.
		 *
		 * @param GithubConfiguration $configuration
		 * @param ObjectManager       $manager
		 */
		public function __construct(GithubConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::ARMOR);

			$this->manager = $manager;
		}

		/**
		 * @param array $context
		 */
		public function scrape(array $context = []): void {
			$this->progressBar->append(5);

			$this->processBaseData();
			$this->progressBar->advance();

			$this->processCraftData();
			$this->progressBar->advance();

			$this->processSkillsData();
			$this->progressBar->advance();

			$bonusMap = $this->processSetBaseData();
			$this->progressBar->advance();

			$this->processSetBonusData($bonusMap);
			$this->progressBar->advance();

			$this->manager->flush();
		}

		/**
		 * @return void
		 */
		public function processBaseData(): void {
			$uri = $this->getUriWithPath('/armor_base.csv');
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$reader = new CsvReader($response->getBody()->getContents(), [
				'name_en' => [ArmorHelper::class, 'replaceSuffixSymbol'],
				'gender' => function(string $value): string {
					return strtolower($value);
				},
				'type' => function(string $value): string {
					$value = strtolower($value);

					if ($value === 'arms')
						$value = ArmorType::GLOVES;

					return $value;
				},
			]);

			$this->progressBar->append($reader->getRowCount());

			while ($row = $reader->read()) {
				$rank = ArmorHelper::getRank($row['name_en']);
				$rarity = (int)$row['rarity'];

				$armor = $this->getArmor($row['name_en']);
				
				if (!$armor) {
					if (!ArmorType::isValid($row['type']))
						throw new \RuntimeException('Invalid armor type ' . $row['type'] . ' for ' . $row['name_en']);

					$armor = new Armor($row['name_en'], $row['type'], $rank, $rarity);

					$this->manager->persist($armor);
					$this->armorCache[$row['name_en']] = $armor;
				} else {
					$armor
						->setRank($rank)
						->setRarity($rarity);
				}

				$armor->getDefense()
					->setBase((int)$row['defense_base'])
					->setMax((int)$row['defense_max'])
					->setAugmented((int)$row['defense_augment_max']);

				$resistance = $armor->getResistances();

				foreach (Element::DAMAGE as $element) {
					$method = 'set' . ucfirst($element);

					call_user_func([$resistance, $method], $row['defense_' . $element]);
				}

				$armor->getSlots()->clear();

				for ($i = 1; $i <= 3; $i++) {
					$value = (int)$row['slot_' . $i];

					if (!$value)
						continue;

					$armor->getSlots()->add(new Slot($value));
				}

				if ($row['gender'] !== 'both') {
					if (!Gender::isValid($row['gender']))
						throw new \RuntimeException('Invalid gender ' . $row['gender'] . ' for ' . $armor->getName());

					$armor->setAttribute(Attribute::REQUIRED_GENDER, $row['gender']);
				}

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @return void
		 */
		protected function processCraftData(): void {
			$uri = $this->getUriWithPath('/armor_craft_ext.csv');
			$resposne = $this->getWithRetry($uri);

			if ($resposne->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$reader = new CsvReader($resposne->getBody()->getContents(), [
				'base_name_en' => [ArmorHelper::class, 'replaceSuffixSymbol'],
			]);

			$this->progressBar->append($reader->getRowCount());

			while ($row = $reader->read()) {
				$armor = $this->getArmor($row['base_name_en']);

				if (!$armor) {
					throw new \RuntimeException('Found armor ' . $row['base_name_en'] . ' in crafting data that was ' .
						'not present in base file');
				}

				$crafting = $armor->getCrafting();

				if (!$crafting)
					$armor->setCrafting($crafting = new ArmorCraftingInfo());
				else
					$crafting->getMaterials()->clear();

				for ($i = 1; $i <= 4; $i++) {
					$keyPrefix = 'item' . $i . '_';

					if (!$row[$keyPrefix . 'name'])
						continue;

					$item = $this->manager->getRepository('App:Item')->findOneBy([
						'name' => $row[$keyPrefix . 'name'],
					]);

					if (!$item) {
						throw new \RuntimeException('Could not find item named ' . $row[$keyPrefix . 'name'] . ' for ' .
							$row['base_name_en']);
					}

					$crafting->getMaterials()->add(new CraftingMaterialCost($item, (int)$row[$keyPrefix . 'qty']));
				}

				$this->progressBar->advance();
			}
		}

		/**
		 * @return void
		 */
		protected function processSkillsData(): void {
			$uri = $this->getUriWithPath('/armor_skills_ext.csv');
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$reader = new CsvReader($response->getBody()->getContents(), [
				'base_name_en' => [ArmorHelper::class, 'replaceSuffixSymbol'],
			]);

			$this->progressBar->append($reader->getRowCount());

			while ($row = $reader->read()) {
				$armor = $this->getArmor($row['base_name_en']);

				if (!$armor) {
					throw new \RuntimeException('Found armor ' . $row['base_name_en'] . ' in skills data that was ' .
						'not present in base file');
				}

				$armor->getSkills()->clear();

				for ($i = 1; $i <= 2; $i++) {
					$keyPrefix = 'skill' . $i . '_';

					if (!$row[$keyPrefix . 'name'])
						continue;

					$skill = $this->manager->getRepository('App:Skill')->findOneBy([
						'name' => $row[$keyPrefix . 'name'],
					]);

					if (!$skill) {
						throw new \RuntimeException(sprintf('Could not find skill named %s in %s',
							$row[$keyPrefix . 'name'], $row['base_name_en']));
					}

					$rank = $skill->getRank((int)$row[$keyPrefix . 'pts']);

					if (!$rank) {
						throw new \RuntimeException(sprintf('%s does not have a rank %s in %s', $skill->getName(),
							$row[$keyPrefix . 'pts'], $row[$keyPrefix . 'name']));
					}

					$armor->getSkills()->add($rank);
				}

				$this->progressBar->advance();
			}
		}

		/**
		 * @return ArmorSet[]
		 */
		protected function processSetBaseData(): array {
			$uri = $this->getUriWithPath('/armorset_base.csv');
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$reader = new CsvReader($response->getBody()->getContents(), [
				'name_en' => [ArmorHelper::class, 'replaceSuffixSymbol'],
				'head' => [ArmorHelper::class, 'replaceSuffixSymbol'],
				'chest' => [ArmorHelper::class, 'replaceSuffixSymbol'],
				'arms' => [ArmorHelper::class, 'replaceSuffixSymbol'],
				'waist' => [ArmorHelper::class, 'replaceSuffixSymbol'],
				'legs' => [ArmorHelper::class, 'replaceSuffixSymbol'],
			]);

			$this->progressBar->append($reader->getRowCount());

			$bonusMap = [];

			while ($row = $reader->read()) {
				$set = $this->getArmorSet($row['name_en']);

				if (!$set) {
					$set = new ArmorSet($row['name_en'], $row['rank'] === 'LR' ? ArmorRank::LOW : ArmorRank::HIGH);

					$this->manager->persist($set);
					$this->armorSetCache[$row['name_en']] = $set;
				}

				$set->getPieces()->clear();

				foreach (ArmorType::ALL as $type) {
					$type = $type === ArmorType::GLOVES ? 'arms' : $type;

					if (!$row[$type])
						continue;

					$armor = $this->getArmor($row[$type]);

					if (!$armor) {
						throw new \RuntimeException('Could not find armor named ' . $row[$type] . ' in ' .
							$row['name_en']);
					}

					$set->getPieces()->add($armor);
				}

				if ($bonus = $row['bonus']) {
					if (!isset($bonusMap[$bonus]))
						$bonusMap[$bonus] = [];

					$bonusMap[$bonus][] = $set;
				}

				$this->progressBar->advance();
			}

			return $bonusMap;
		}

		/**
		 * @param ArmorSet[] $bonusMap
		 *
		 * @return void
		 */
		protected function processSetBonusData(array $bonusMap): void {
			$uri = $this->getUriWithPath('/armorset_bonus_base.csv');
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$reader = new CsvReader($response->getBody()->getContents());

			$this->progressBar->append($reader->getRowCount());

			while ($row = $reader->read()) {
				if (!isset($bonusMap[$row['name_en']]))
					continue;

				/** @var ArmorSetBonus|null $bonus */
				$bonus = $this->manager->getRepository('App:ArmorSetBonus')->findOneBy([
					'name' => $row['name_en'],
				]);

				if (!$bonus) {
					$bonus = new ArmorSetBonus($row['name_en']);

					$this->manager->persist($bonus);
				} else
					$bonus->getRanks()->clear();

				for ($i = 1; $i <= 2; $i++) {
					$prefix = 'skill' . $i . '_';

					$name = $row[$prefix . 'name'];
					$required = $row[$prefix . 'required'];

					if (!$name)
						continue;

					/** @var Skill|null $skill */
					$skill = $this->manager->getRepository('App:Skill')->findOneBy([
						'name' => $name,
					]);

					if (!$skill) {
						throw new \RuntimeException(sprintf('Could not find skill named %s in %s', $name,
							$row['name_en']));
					}

					$bonus->getRanks()->add(new ArmorSetBonusRank($bonus, $required, $skill->getRank(1)));
				}

				/** @var ArmorSet $set */
				foreach ($bonusMap[$bonus->getName()] as $set)
					$set->setBonus($bonus);

				$this->progressBar->advance();
			}
		}

		/**
		 * @param string $name
		 *
		 * @return Armor|null
		 */
		protected function getArmor(string $name): ?Armor {
			if (array_key_exists($name, $this->armorCache))
				return $this->armorCache[$name];

			return $this->armorCache[$name] = $this->manager->getRepository('App:Armor')->findOneBy([
				'name' => $name,
			]);
		}

		/**
		 * @param string $name
		 *
		 * @return ArmorSet|null
		 */
		protected function getArmorSet(string $name): ?ArmorSet {
			if (array_key_exists($name, $this->armorSetCache))
				return $this->armorSetCache[$name];

			return $this->manager->getRepository('App:ArmorSet')->findOneBy([
				'name' => $name,
			]);
		}

		/**
		 * @param string $path
		 *
		 * @return UriInterface
		 */
		protected function getUriWithPath(string $path): UriInterface {
			return parent::getUriWithPath('/armors/' . ltrim($path, '/'));
		}
	}