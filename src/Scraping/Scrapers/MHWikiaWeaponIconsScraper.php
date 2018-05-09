<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Asset;
	use App\Entity\WeaponAssets;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\MHWikiaConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\ScraperInterface;
	use App\Scraping\Scrapers\Helpers\MHWikiaHelper;
	use App\Scraping\Type;
	use App\Utility\StringUtil;
	use Aws\S3\S3Client;
	use Aws\Sdk;
	use Doctrine\Common\Persistence\ObjectManager;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class MHWikiaWeaponIconsScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		/**
		 * @var MHWikiaConfiguration
		 */
		protected $configuration;

		/**
		 * @var EntityManagerInterface|ObjectManager
		 */
		protected $manager;

		/**
		 * @var S3Client
		 */
		protected $s3Client;

		/**
		 * @var Asset[]
		 */
		protected $assetCache = [];

		/**
		 * MHWikiaWeaponIconsScraper constructor.
		 *
		 * @param MHWikiaConfiguration $configuration
		 * @param ObjectManager        $manager
		 * @param Sdk                  $aws
		 */
		public function __construct(MHWikiaConfiguration $configuration, ObjectManager $manager, Sdk $aws) {
			parent::__construct($configuration, Type::WEAPON_ICONS);

			$this->manager = $manager;
			$this->s3Client = $aws->createS3();
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(array $context = []): void {
			$subtypes = $context[ScraperInterface::CONTEXT_SUBTYPES] ?? [];

			$this->progressBar->append($subtypes ? sizeof($subtypes) : sizeof(MHWikiaHelper::WEAPON_TREE_PATHS));

			foreach (MHWikiaHelper::WEAPON_TREE_PATHS as $weaponType => $path) {
				if ($subtypes && !in_array($weaponType, $subtypes))
					continue;

				$uri = $this->configuration->getBaseUri()->withPath($path);
				$response = $this->getWithRetry($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$crawler = (new Crawler($response->getBody()->getContents()))
					->filter('#mw-content-text > .wikitable:not(.hover)');

				for ($i = 0, $ii = $crawler->count(); $i < $ii; $i++) {
					$cells = $crawler->eq($i)->filter('td');
					$title = StringUtil::clean($cells->first()->text());

					if (stripos($title, 'rarity legend') === false) {
						if ($i + 1 === $ii)
							throw new \RuntimeException('Could not find rarity table on ' . $uri);

						continue;
					}

					$this->process($weaponType, $cells->slice(1));

					break;
				}

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param string  $weaponType
		 * @param Crawler $cells
		 *
		 * @return void
		 */
		protected function process(string $weaponType, Crawler $cells): void {
			for ($i = 0, $ii = $cells->count(); $i < $ii; $i++) {
				$cell = $cells->eq($i);

				$iconUri = trim($cell->filter('a')->attr('href'));
				$iconUri = substr($iconUri, 0, strpos($iconUri, '.png') + 4);

				$icon = imagecreatefrompng($iconUri);
				imagealphablending($icon, true);
				imagesavealpha($icon, true);

				$tmp = tmpfile();

				imagepng($icon, $tmp);
				imagedestroy($icon);

				$tmpUri = stream_get_meta_data($tmp)['uri'];

				$primary = hash_file('md5', $tmpUri);
				$secondary = hash_file('sha1', $tmpUri);

				$asset = $this->getAsset($primary, $secondary);

				if (!$asset) {
					$fileKey = sprintf('%s.%s', $primary, $secondary);
					$bucketKey = sprintf('weapons/%s/icons/%s.png', $weaponType, $fileKey);

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

					$this->manager->persist($asset);
				}

				$weapons = $this->manager->getRepository('App:Weapon')->findBy([
					'type' => $weaponType,
					'rarity' => $i + 1,
				]);

				/** @var Asset[] $removedAssets */
				$removedAssets = [];

				foreach ($weapons as $weapon) {
					if ($group = $weapon->getAssets()) {
						if ($group->getIcon() === $asset)
							continue;

						$removedAssets[] = $group->getIcon();

						$group->setIcon($asset);
					} else
						$weapon->setAssets(new WeaponAssets($asset, null));
				}

				foreach ($removedAssets as $removed) {
					/** @var int $check */
					$check = $this->manager->createQueryBuilder()
						->from('App:WeaponAssets', 'a')
						->select('COUNT(a)')
						->where('a.icon = :removed')
						->setParameter('removed', $removed)
						->getQuery()
							->getSingleScalarResult();

					if ($check > 0)
						continue;

					$this->s3Client->deleteObject([
						'Bucket' => 'assets.mhw-db.com',
						'Key' => substr(parse_url($removed->getUri(), PHP_URL_PATH), 1),
					]);

					$this->manager->remove($removed);
				}
			}
		}

		/**
		 * @param string $primaryHash
		 * @param string $secondaryHash
		 *
		 * @return Asset|null
		 */
		public function getAsset(string $primaryHash, string $secondaryHash): ?Asset {
			$key = sprintf('%s.%s', $primaryHash, $secondaryHash);

			if (array_key_exists($key, $this->assetCache))
				return $this->assetCache[$key];

			$asset = $this->manager->getRepository('App:Asset')->findOneBy([
				'primaryHash' => $primaryHash,
				'secondaryHash' => $secondaryHash,
			]);

			return $this->assetCache[$key] = $asset;
		}
	}