<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\CraftingMaterialCost;
	use App\Entity\Slot;
	use App\Entity\Weapon;
	use App\Entity\WeaponCraftingInfo;
	use App\Game\AmmoType;
	use App\Game\Attribute;
	use App\Game\BowCoatingType;
	use App\Game\BowgunDeviation;
	use App\Game\DamageType;
	use App\Game\Elderseal;
	use App\Game\Element;
	use App\Game\InsectGlaiveBoostType;
	use App\Game\RawDamageMultiplier;
	use App\Game\BowgunSpecialAmmo;
	use App\Game\WeaponType;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\MHWikiaConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\ScraperInterface;
	use App\Scraping\Scrapers\Helpers\MHWikiaHelper;
	use App\Scraping\Type;
	use App\Utility\StringUtil;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class MHWikiaWeaponScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * @var Weapon[]
		 */
		protected $weaponCache = [];

		/**
		 * MHWikiaWeaponScraper constructor.
		 *
		 * @param MHWikiaConfiguration $configuration
		 * @param ObjectManager        $manager
		 */
		public function __construct(MHWikiaConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::WEAPONS);

			$this->manager = $manager;
		}

		/**
		 * @param array $context
		 */
		public function scrape(array $context = []): void {
			$subtypes = $context[ScraperInterface::CONTEXT_SUBTYPES] ?? [];

			$this->progressBar->append($subtypes ? sizeof($subtypes) : sizeof(MHWikiaHelper::WEAPON_TREE_PATHS));

			foreach (MHWikiaHelper::WEAPON_TREE_PATHS as $weaponType => $path) {
				if ($subtypes && !in_array($weaponType, $subtypes))
					continue;

				$uri = $this->getConfiguration()->getBaseUri()->withPath($path);
				$response = $this->getWithRetry($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$table = (new Crawler($response->getBody()->getContents()))
					->filter('#mw-content-text .wikitable.hover');

				if (!$table->count())
					throw new \RuntimeException('Could not find weapon tree table');

				$rows = $table->first()->filter('tr');

				$this->progressBar->append($rows->count());

				for ($i = 0, $ii = $rows->count(); $i < $ii; $i++) {
					$link = $rows->eq($i)->filter('td:first-child a')->last();

					if (!$link->count()) {
						$this->progressBar->advance();

						continue;
					}

					$this->process(parse_url($link->attr('href'), PHP_URL_PATH), $weaponType);

					$this->progressBar->advance();
				}

				$this->manager->flush();

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param string $path
		 * @param string $weaponType
		 */
		public function process(string $path, string $weaponType): void {
			$uri = $this->getConfiguration()->getBaseUri()->withPath($path);
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			/**
			 * For melee weapons:
			 *   0 = Main Data
			 *   1 = Crafting Progression
			 *
			 * For ranged weapons:
			 *   0 = Main Data
			 *   1 = Ammo Info (capacities for bowguns, coatings for bows)
			 *   2 = Crafting Progression
			 */
			$blocks = (new Crawler($response->getBody()->getContents()))->filter('#mw-content-text aside');

			$mainBlock = $blocks->eq(0);

			$name = trim(explode('/', $mainBlock->filter('h2')->text())[0]);
			$name = StringUtil::replaceNumeralRank($name);

			/**
			 * 0 = General data
			 * 1 = Buy info
			 * 2 = Creation / upgrade material costs
			 * 3 = Creation / upgrade zenny costs
			 */
			$mainBlockSections = $mainBlock->filter('section');

			if ($mainBlockSections->count() !== 4) {
				throw new \RuntimeException('Something is wrong with main block: found ' . $mainBlockSections->count() .
					' section tag(s) on ' . $uri);
			}

			$generalStats = MHWikiaHelper::parseHtmlToKeyValuePairs($mainBlockSections->eq(0)->children());

			$weapon = $this->manager->getRepository('App:Weapon')->findOneBy([
				'name' => $name,
				'type' => $weaponType,
			]);

			if ($weapon)
				$weapon->setRarity((int)$generalStats['rarity']);
			else {
				$weapon = new Weapon($name, $weaponType, (int)$generalStats['rarity']);

				$this->manager->persist($weapon);
				$this->weaponCache[$weaponType . ':' . $name] = $weapon;
			}
			
			$weapon->setAttribute(Attribute::DAMAGE_TYPE, DamageType::getWeaponDamageType($weaponType));

			$attack = (int)$generalStats['attack'];

			if (!$attack)
				throw new \RuntimeException('Could not find attack value on ' . $uri);

			$weapon->getAttack()
				->setDisplay($attack)
				->setRaw((int)($attack / RawDamageMultiplier::get($weaponType)));

			$weapon->removeAttribute(Attribute::AFFINITY);

			if ($affinity = $generalStats['affinity'] ?? null)
				$weapon->setAttribute(Attribute::AFFINITY, StringUtil::toNumber($affinity));

			$weapon->removeAttribute(Attribute::DEFENSE);

			if ($defense = $generalStats['defense'] ?? null)
				$weapon->setAttribute(Attribute::DEFENSE, StringUtil::toNumber($defense));

			$weapon->removeAttribute(Attribute::ELDERSEAL);

			if ($elderseal = $generalStats['elderseal'] ?? null) {
				$elderseal = strtolower($elderseal);

				if (!Elderseal::isValid($elderseal))
					throw new \RuntimeException('Invalid value found for elderseal: ' . $generalStats['elderseal']);

				$weapon->setAttribute(Attribute::ELDERSEAL, $elderseal);
			}

			$weapon->getSlots()->clear();

			if ($slots = $generalStats['slots'] ?? null) {
				$slots = preg_replace('/[^\\d]/', '', $slots);

				for ($i = 0, $ii = strlen($slots); $i < $ii; $i++)
					$weapon->getSlots()->add(new Slot((int)$slots[$i]));
			}

			$elementTypes = [];

			if ($rawElement = $generalStats['special'] ?? null) {
				preg_match_all('/\\(?[A-Za-z\\s]+ \d+\\)?/', $rawElement, $matches);

				foreach ($matches as $match) {
					$value = strtolower($match[0]);

					if (strpos($value, '(') === 0) {
						$hidden = true;
						$value = trim(str_replace(['(', ')'], '', $value));
					} else
						$hidden = false;

					$damage = (int)substr($value, strrpos($value, ' ') + 1);
					$element = trim(str_replace($damage, '', $value));

					if ($element === 'blastblight')
						$element = Element::BLAST;

					if (!Element::isValid($element))
						throw new \RuntimeException('Invalid value found for type in element: ' . $match[0]);

					$weapon->setElement($element, $damage, $hidden);

					$elementTypes[] = $element;
				}
			}

			$removed = $weapon->getElements()
				->matching(Criteria::create()->where(Criteria::expr()->notIn('type', $elementTypes)));

			foreach ($removed as $item)
				$weapon->getElements()->removeElement($item);

			// region Bowgun-specific Properties
			$weapon->removeAttribute(Attribute::DEVIATION);

			if ($deviation = $generalStats['deviation'] ?? null) {
				$deviation = strtolower($generalStats['deviation']);

				if (!BowgunDeviation::isValid($deviation))
					throw new \RuntimeException('Invalid value found for deviation: ' . $generalStats['deviation']);

				$weapon->setAttribute(Attribute::DEVIATION, $deviation);
			}

			$weapon->removeAttribute(Attribute::SPECIAL_AMMO);

			if ($specialAmmo = $generalStats['specialAmmo'] ?? null) {
				$specialAmmo = strtolower($generalStats['specialAmmo']);

				if (!BowgunSpecialAmmo::isValid($specialAmmo))
					throw new \RuntimeException('Invalid value found for special ammo: ' . $generalStats['specialAmmo']);

				$weapon->setAttribute(Attribute::SPECIAL_AMMO, $specialAmmo);
			}

			$weapon->removeAttribute(Attribute::AMMO_CAPACITIES);

			if (WeaponType::isBowgun($weaponType)) {
				$rows = $blocks->eq(1)->filter('section tr');
				$capacities = [];

				for ($i = 0, $ii = $rows->count(); $i < $ii; $i++) {
					$cells = $rows->eq($i)->filter('td');

					if (!$cells->count())
						continue;

					$ammoType = strtolower(StringUtil::clean($cells->eq(0)->text()));
					$ammoType = trim(str_replace('ammo', '', $ammoType));

					if (!AmmoType::isValid($ammoType))
						throw new \RuntimeException('Invalid value found for ammo type: ' . $ammoType);

					$capacities[$ammoType] = array_map(function(string $item): int {
						return (int)trim($item);
					}, explode('/', StringUtil::clean($cells->eq(1)->text())));
				}

				$weapon->setAttribute(Attribute::AMMO_CAPACITIES, $capacities);
			}
			// endregion

			// region Bow-specific Properties
			$weapon->removeAttribute(Attribute::COATINGS);

			if ($weaponType === WeaponType::BOW) {
				$values = $blocks->eq(1)->filter('section b');
				$coatings = [];

				for ($i = 0, $ii = $values->count(); $i < $ii; $i++) {
					$value = strtolower(StringUtil::clean($values->eq($i)->text()));
					$value = trim(str_replace('coating', '', $value));

					if ($value === 'c.range')
						$value = BowCoatingType::CLOSE_RANGE;
					else if ($value === 'para')
						$value = BowCoatingType::PARALYSIS;
					else if (!BowCoatingType::isValid($value))
						throw new \RuntimeException('Invalid value found for coating type: ' . $value);

					$coatings[] = $value;
				}

				$weapon->setAttribute(Attribute::COATINGS, $coatings);
			}
			// endregion

			// region Insect Glaive-specific Properties
			$weapon->removeAttribute(Attribute::IG_BOOST_TYPE);

			if ($weaponType === WeaponType::INSECT_GLAIVE) {
				$boostType = strtolower(StringUtil::clean($generalStats['kinsectBonus']));
				$boostType = trim(str_replace('boost', '', $boostType));

				if (!InsectGlaiveBoostType::isValid($boostType)) {
					throw new \RuntimeException('Invalid value found for IG boost type: ' .
						$generalStats['kinsectBonus'] . ' on ' . $uri);
				}

				$weapon->setAttribute(Attribute::IG_BOOST_TYPE, $boostType);
			}
			// endregion

			// region Gunlance-specific Properties
			$weapon->removeAttribute(Attribute::GL_SHELLING_TYPE);

			if ($weaponType === WeaponType::GUNLANCE) {
				$shellingType = trim(StringUtil::clean($generalStats['shellingType']));
				$shellingType = str_replace('Lv ', 'Lv', $shellingType);

				$weapon->setAttribute(Attribute::GL_SHELLING_TYPE, $shellingType);
			}
			// endregion

			$weapon->removeAttribute(Attribute::PHIAL_TYPE);

			if ($weapon === WeaponType::SWITCH_AXE || $weaponType === WeaponType::CHARGE_BLADE)
				$weapon->setAttribute(Attribute::PHIAL_TYPE, trim(StringUtil::clean($generalStats['phialType'])));

			/**
			 * 0 = Crafting material costs
			 * 1 = Upgrade material costs
			 */
			$materialCostCells = $mainBlockSections->eq(2)->filter('td');

			$craftingCosts = MHWikiaHelper::parseItemList($materialCostCells->eq(0));
			$craftable = $craftingCosts !== null;

			$crafting = $weapon->getCrafting();

			if (!$crafting)
				$weapon->setCrafting($crafting = new WeaponCraftingInfo($craftable));
			else
				$crafting->setCraftable($craftable);

			$crafting->getBranches()->clear();
			$crafting->getCraftingMaterials()->clear();

			if ($craftingCosts !== null) {
				foreach ($craftingCosts as $itemName => $quantity) {
					$item = $this->manager->getRepository('App:Item')->findOneBy([
						'name' => $itemName,
					]);

					if (!$item)
						throw new \RuntimeException('Could not find item named ' . $itemName . ' on ' . $uri);

					$crafting->getCraftingMaterials()->add(new CraftingMaterialCost($item, $quantity));
				}
			}

			$crafting->getUpgradeMaterials()->clear();

			$upgradeCosts = MHWikiaHelper::parseItemList($materialCostCells->eq(1));

			if ($upgradeCosts !== null) {
				foreach ($upgradeCosts as $itemName => $quantity) {
					$item = $this->manager->getRepository('App:Item')->findOneBy([
						'name' => $itemName,
					]);

					if (!$item)
						throw new \RuntimeException('Could not find item named ' . $itemName . ' on ' . $uri);

					$crafting->getUpgradeMaterials()->add(new CraftingMaterialCost($item, $quantity));
				}
			}

			/**
			 * 0 = Previous item
			 * 1 = Current item
			 * 2 = Next items
			 */
			$progressionCells = $blocks->last()->filter('td');

			$previousLink = $progressionCells->eq(0)->filter('a:not(.image)');

			if ($previousLink->count()) {
				if (!$crafting->getUpgradeMaterials()->count())
					throw new \RuntimeException('Previous weapon (crafting) found, but no upgrade materials in list');

				$previousName = StringUtil::clean($previousLink->text());
				$previousName = StringUtil::replaceNumeralRank($previousName);

				$previousWeapon = $this->getWeapon($previousName, $weaponType);

				if (!$previousWeapon) {
					throw new \RuntimeException('Could not find weapon (for previous craft) named ' . $previousName .
						' on ' . $uri);
				}

				$crafting->setPrevious($previousWeapon);

				if (!$previousWeapon->getCrafting()->getBranches()->contains($weapon))
					$previousWeapon->getCrafting()->getBranches()->add($weapon);
			} else if ($crafting->getUpgradeMaterials()->count()) {
				throw new \RuntimeException('No previous weapon (crafting) specified, but upgrade list is populated on ' .
					$uri);
			}
		}

		/**
		 * @param string $name
		 * @param string $type
		 *
		 * @return Weapon|null
		 */
		protected function getWeapon(string $name, string $type): ?Weapon {
			$key = $type . ':' . $name;

			if (array_key_exists($key, $this->weaponCache))
				return $this->weaponCache[$key];

			return $this->weaponCache[$key] = $this->manager->getRepository('App:Weapon')->findOneBy([
				'name' => $name,
				'type' => $type,
			]);
		}
	}