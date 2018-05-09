<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Asset;
	use App\Entity\Weapon;
	use App\Entity\WeaponAssets;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\FextralifeConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\ScraperInterface;
	use App\Scraping\Scrapers\Helpers\FextralifeHelper;
	use App\Scraping\Type;
	use App\Utility\RomanNumeral;
	use Aws\S3\S3Client;
	use Aws\Sdk;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class FextralifeWeaponImagesScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		/**
		 * @var FextralifeConfiguration
		 */
		protected $configuration;

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * @var S3Client
		 */
		protected $s3Client;

		/**
		 * @var Asset[]
		 */
		private $assetCache = [];

		/**
		 * FextralifeWeaponImagesScraper constructor.
		 *
		 * @param FextralifeConfiguration $configuration
		 * @param ObjectManager           $manager
		 * @param Sdk                     $aws
		 */
		public function __construct(FextralifeConfiguration $configuration, ObjectManager $manager, Sdk $aws) {
			parent::__construct($configuration, Type::WEAPON_IMAGES);

			$this->manager = $manager;
			$this->s3Client = $aws->createS3();
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(array $context = []): void {
			$subtypes = $context[ScraperInterface::CONTEXT_SUBTYPES] ?? [];

			if ($subtypes) {
				$weapons = $this->manager->getRepository('App:Weapon')->findBy([
					'type' => $subtypes,
				]);
			} else
				$weapons = $this->manager->getRepository('App:Weapon')->findAll();

			$this->progressBar->append(sizeof($weapons));

			foreach ($weapons as $weapon) {
				$this->process($weapon);

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param Weapon $weapon
		 *
		 * @return void
		 */
		protected function process(Weapon $weapon): void {
			$slug = str_replace([
				' ',
				'(',
				')',
				'"',
			], [
				'+',
				'',
				'',
				'',
			], preg_replace_callback('/\d+$/', function(array $matches) {
				return RomanNumeral::toNumeral((int)$matches[0]);
			}, FextralifeHelper::toWikiWeaponName($weapon->getName())));

			$uri = $this->getConfiguration()->getBaseUri()->withPath('/' . $slug);
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK) {
				$this->write('[WeaponImages] Could not retrieve ' . $uri . ' (' . $response->getStatusCode() . ')');

				return;
			}

			$imageNode = (new Crawler($response->getBody()->getContents()))->filter('#infobox img')->first();
			$path = $imageNode->attr('src');

			if (stripos($path, 'http') !== 0)
				$path = (string)$this->configuration->getBaseUri()->withPath($path);

			$image = imagecreatefrompng($path);
			imagealphablending($image, true);
			imagesavealpha($image, true);

			$tmp = tmpfile();

			imagepng($image, $tmp);
			imagedestroy($image);

			$tmpUri = stream_get_meta_data($tmp)['uri'];

			$primary = hash_file('sha1', $tmpUri);
			$secondary = hash_file('md5', $tmpUri);

			$asset = $this->getAsset($primary, $secondary);

			if (!$asset) {
				$fileKey = sprintf('%s.%s', $primary, $secondary);
				$bucketKey = sprintf('weapons/%s/%s.png', $weapon->getType(), $fileKey);

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

			if ($group = $weapon->getAssets()) {
				$existing = $group->getImage();

				if (!$existing)
					$group->setImage($asset);
				else if ($existing !== $asset) {
					$this->s3Client->deleteObject([
						'Bucket' => 'assets.mhw-db.com',
						'Key' => substr(parse_url($existing->getUri(), PHP_URL_PATH), 1),
					]);

					$this->manager->remove($existing);

					$group->setImage($asset);
				}
			} else
				$weapon->setAssets(new WeaponAssets(null, $asset));
		}

		/**
		 * @param string $primaryHash
		 * @param string $secondaryHash
		 *
		 * @return Asset|null
		 */
		protected function getAsset(string $primaryHash, string $secondaryHash): ?Asset {
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