<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Armor;
	use App\Entity\ArmorAssets;
	use App\Entity\ArmorCraftingInfo;
	use App\Entity\ArmorSet;
	use App\Entity\Asset;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\Slot;
	use App\Game\ArmorRank;
	use App\Game\Attribute;
	use App\Game\Gender;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\KiranicoConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\Scrapers\Helpers\KiranicoArmorHelper;
	use App\Scraping\Scrapers\Helpers\KiranicoHelper;
	use App\Scraping\Scrapers\Helpers\SpriteMap;
	use App\Scraping\Type;
	use App\Utility\StringUtil;
	use Aws\S3\S3Client;
	use Aws\Sdk;
	use Doctrine\Common\Persistence\ObjectManager;
	use Doctrine\ORM\EntityManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoArmorScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		/**
		 * @var ObjectManager|EntityManager
		 */
		protected $manager;

		/**
		 * @var S3Client
		 */
		protected $s3Client;

		/**
		 * @var ArmorSet[]
		 */
		protected $setCache = [];

		/**
		 * @var Asset[]
		 */
		protected $assetCache = [];

		/**
		 * KiranicoArmorScraper constructor.
		 *
		 * @param KiranicoConfiguration $configuration
		 * @param ObjectManager         $manager
		 * @param Sdk                   $aws
		 */
		public function __construct(KiranicoConfiguration $configuration, ObjectManager $manager, Sdk $aws) {
			parent::__construct($configuration, Type::ARMOR);

			$this->manager = $manager;
			$this->s3Client = $aws->createS3();
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(array $context = []): void {
			$uri = $this->configuration->getBaseUri()->withPath('/armor');
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container .tab-content .card');
			$count = $crawler->count();

			$this->progressBar->append($count);

			$currentRank = ArmorRank::LOW;
			$spriteMaps = [
				Gender::MALE => new SpriteMap('https://mhworld.kiranico.com/images/armor_m.png'),
				Gender::FEMALE => new SpriteMap('https://mhworld.kiranico.com/images/armor_f.png'),
			];

			$setsContext = $context['sets'] ?? [];

			for ($i = 0; $i < $count; $i++) {
				$setNode = $crawler->eq($i);

				$setName = StringUtil::clean($setNode->filter('.card-header')->text());
				$setName = trim(str_replace('Set', '', $setName));

				if ($setsContext && !in_array($setName, $setsContext)) {
					$this->progressBar->advance();

					continue;
				}

				$set = $this->getArmorSet($setName);

				if (!$set) {
					if (strpos($setName, 'Alpha') || strpos($setName, 'Beta'))
						$rank = ArmorRank::HIGH;
					else
						$rank = ArmorRank::LOW;

					$this->setCache[$setName] = $set = new ArmorSet($setName, $rank);

					$this->manager->persist($set);
				}

				$setPieceNodes = $setNode->filter('.card-body table')->eq(0)->filter('tr');

				for ($j = 0, $jj = $setPieceNodes->count(); $j < $jj; $j++) {
					$link = $setPieceNodes->eq($j)->filter('a')->attr('href');

					if (stripos($link, 'Alpha'))
						$currentRank = ArmorRank::HIGH;

					$this->process(parse_url($link, PHP_URL_PATH), $currentRank, $set, $spriteMaps);
				}

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param string      $path
		 * @param string      $rank
		 * @param ArmorSet    $armorSet
		 * @param SpriteMap[] $spriteMaps
		 *
		 * @return void
		 */
		protected function process(string $path, string $rank, ArmorSet $armorSet, array $spriteMaps): void {
			$uri = $this->configuration->getBaseUri()->withPath($path);
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			/**
			 * 0 = Top navbar
			 * 1 = Main section (name, description, attributes, etc.)
			 * 2 = Resistances
			 * 3 = Skills
			 * 4 = Crafting info
			 * 5 = Bottom navbar
			 */
			$sections = (new Crawler($response->getBody()->getContents()))->filter('.container .col-lg-9.px-2')
				->filter('.card');

			$mainSection = $sections->filter('.card')->eq(1);

			// region Armor Lookup / Init
			$rawName = $mainSection->filter('.media h1[itemprop=name]')->text();

			/**
			 * @var string $name
			 * @var string $armorType
			 */
			list($name, $armorType) = KiranicoArmorHelper::parseArmorName($rawName);

			/** @var Armor|null $armor */
			$armor = $this->manager->getRepository('App:Armor')->findOneBy([
				'name' => $name,
			]);

			/**
			 * 0 = Defense
			 * 1 = Slots
			 * 2 = Price
			 * 3 = Gender
			 * 4 = Rarity
			 */
			$infoNodes = $mainSection->filter('.card-footer .p-3');

			$rarity = (int)StringUtil::clean($infoNodes->eq(4)->filter('.lead')->text());

			if (!$armor) {
				$armor = new Armor($name, $armorType, $rank, $rarity);

				$this->manager->persist($armor);
			} else {
				$armor
					->setRarity($rarity)
					->setAttributes([]);

				$armor->getSkills()->clear();
			}
			// endregion

			// region Assets
			$assetNodes = $mainSection->filter('.card-body .media .img-thumbnail');

			/** @var Asset[] $assets */
			$assets = [];

			foreach ([Gender::MALE, Gender::FEMALE] as $i => $gender) {
				$tmp = tmpfile();
				$tmpUri = stream_get_meta_data($tmp)['uri'];

				preg_match('/background: .* (-?\d+)px (-?\d+)px/', $assetNodes->eq($i)->attr('style'),
					$matches);

				if (sizeof($matches) < 3)
					continue;

				$sprite = $spriteMaps[$gender]->get((int)$matches[1], (int)$matches[2], 96, 96);

				if ($sprite === null)
					continue;

				imagepng($sprite, $tmp);
				imagedestroy($sprite);

				$primary = hash_file('md5', $tmpUri);
				$secondary = hash_file('sha1', $tmpUri);

				$asset = $this->getAsset($primary, $secondary);

				if ($asset === null) {
					$fileKey = sprintf('%s.%s', $primary, $secondary);
					$bucketKey = 'armor/' . $fileKey . '.png';

					if (!$this->s3Client->doesObjectExist('assets.mhw-db.com', $bucketKey))
						$this->s3Client->putObject([
							'Bucket' => 'assets.mhw-db.com',
							'Key' => $bucketKey,
							'ContentType' => 'image/png',
							'Body' => $tmp,
						]);

					fclose($tmp);

					$asset = new Asset('https://assets.mhw-db.com/' . $bucketKey, $primary, $secondary);

					$this->assetCache[$fileKey] = $asset;
				}

				$assets[$gender] = $asset;
			}

			if ($group = $armor->getAssets()) {
				if (isset($assets[Gender::MALE])) {
					if ($group->getImageMale() !== $assets[Gender::MALE]) {
						if ($existing = $group->getImageMale())
							$this->deleteAsset($existing);

						$group->setImageMale($assets[Gender::MALE]);
					}
				} else if ($asset = $group->getImageMale()) {
					$this->deleteAsset($asset);

					$group->setImageMale(null);
				}

				if (isset($assets[Gender::FEMALE])) {
					if ($group->getImageFemale() !== $assets[Gender::FEMALE]) {
						if ($existing = $group->getImageFemale())
							$this->deleteAsset($existing);

						$group->setImageFemale($assets[Gender::FEMALE]);
					}
				} else if ($asset = $group->getImageFemale()) {
					$this->deleteAsset($asset);

					$group->setImageFemale(null);
				}
			} else {
				$group = new ArmorAssets($assets[Gender::MALE] ?? null, $assets[Gender::FEMALE] ?? null);

				$armor->setAssets($group);
			}
			// endregion

			$armor->setArmorSet($armorSet);

			// region Defense
			$defense = (int)strtok(trim($infoNodes->eq(0)->filter('.lead')->text()), ' ');

			$armor->getDefense()->setBase($defense);

			// Both max and augmented are set by another scraper. If the fields are empty (in the case of new armor
			// objects), default them to the same value as the base defense.

			if (!$armor->getDefense()->getMax())
				$armor->getDefense()->setMax($defense);

			if (!$armor->getDefense()->getAugmented())
				$armor->getDefense()->setAugmented($defense);
			// endregion

			// region Slots
			$armor->getSlots()->clear();

			foreach (KiranicoHelper::getSlots($infoNodes->eq(1)->filter('.zmdi')) as $rank)
				$armor->getSlots()->add(new Slot($rank));
			// endregion

			// region Gender Requirements
			$genderNodes = $infoNodes->eq(3)->filter('.zmdi:not(.text-dark)');

			if ($genderNodes->count() < 2) {
				preg_match('/zmdi-(female|male)/', $genderNodes->first()->attr('class'), $matches);

				if (sizeof($matches) >= 2)
					$armor->setAttribute(Attribute::REQUIRED_GENDER, $matches[1]);
			}
			// endregion

			// region Elemental Resistances
			$elemResists = $sections->eq(2)->filter('.card-body table tr');

			for ($i = 0, $ii = $elemResists->count(); $i < $ii; $i++) {
				$children = $elemResists->eq($i)->children();
				$elemText = trim($children->first()->text());

				$elem = trim(substr($elemText, strrpos($elemText, ' ') + 1));
				$value = trim($children->last()->text());

				if (strpos($value, '+') === 0)
					$value = substr($value, 1);

				$value = (int)$value;

				$method = 'set' . ucfirst($elem);

				if (!method_exists($armor->getResistances(), $method))
					throw new \RuntimeException($elem . ' is not a recognized element');

				call_user_func([$armor->getResistances(), $method], $value);
			}
			// endregion

			// region Skills
			$skills = $sections->eq(3)->filter('.card-body table tr');

			for ($i = 0, $ii = $skills->count(); $i < $ii; $i++) {
				$children = $skills->eq($i)->children();

				$skillName = trim($children->first()->text());

				$skill = $this->manager->getRepository('App:Skill')->findOneBy([
					'name' => $skillName,
				]);

				if (!$skill)
					throw new \RuntimeException($skillName . ' is not a known skill');

				$skillRank = trim($children->last()->text());

				if (strpos($skillRank, '+') === 0)
					$skillRank = substr($skillRank, 1);

				$rank = $skill->getRank((int)$skillRank);

				if (!$rank)
					throw new \RuntimeException($skillName . ' has no rank labelled "' . $skillRank . '"');

				$armor->getSkills()->add($rank);
			}
			// endregion

			// region Crafting
			if ($crafting = $armor->getCrafting())
				$crafting->getMaterials()->clear();
			else
				$armor->setCrafting($crafting = new ArmorCraftingInfo());

			$materials = $sections->eq(4)->filter('.card-body tr');

			for ($i = 0, $ii = $materials->count(); $i < $ii; $i++) {
				/**
				 * 0 = Item Name
				 * 1 = Quantity
				 */
				$cells = $materials->eq($i)->children();

				$itemName = StringUtil::clean($cells->eq(0)->text());
				$item = $this->manager->getRepository('App:Item')->findOneBy([
					'name' => $itemName,
				]);

				if (!$item) {
					throw new \RuntimeException(sprintf('[Armor] Could not find item named %s (for %s)', $itemName,
						$armor->getName()));
				}

				$quantity = (int)substr(StringUtil::clean($cells->eq(1)->text()), 1);

				if ($quantity <= 0) {
					throw new \RuntimeException(sprintf('[Armor] Got quantity = %d, expected > 0 (for %s)', $quantity,
						$armor->getName()));
				}

				$crafting->getMaterials()->add(new CraftingMaterialCost($item, $quantity));
			}
			// endregion
		}

		/**
		 * @param string $name
		 *
		 * @return ArmorSet|null
		 */
		protected function getArmorSet(string $name): ?ArmorSet {
			if (isset($this->setCache[$name]))
				return $this->setCache[$name];

			$set = $this->manager->getRepository('App:ArmorSet')->findOneBy([
				'name' => $name,
			]);

			return $this->setCache[$name] = $set;
		}

		/**
		 * @param string $primaryHash
		 * @param string $secondaryHash
		 *
		 * @return Asset|null
		 */
		protected function getAsset(string $primaryHash, string $secondaryHash): ?Asset {
			$key = sprintf('%s.%s', $primaryHash, $secondaryHash);

			if (isset($this->assetCache[$key]))
				return $this->assetCache[$key];

			$asset = $this->manager->getRepository('App:Asset')->findOneBy([
				'primaryHash' => $primaryHash,
				'secondaryHash' => $secondaryHash,
			]);

			return $this->assetCache[$key] = $asset;
		}

		/**
		 * @param Asset $asset
		 *
		 * @return bool
		 */
		protected function deleteAsset(Asset $asset): bool {
			$this->s3Client->deleteObject([
				'Bucket' => 'assets.mhw-db.com',
				'Key' => sprintf('armor/%s.%s.png', $asset->getPrimaryHash(), $asset->getSecondaryHash()),
			]);

			return true;
		}
	}