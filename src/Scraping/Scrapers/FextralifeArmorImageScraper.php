<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Armor;
	use App\Entity\ArmorAssets;
	use App\Entity\Asset;
	use App\Game\Attribute;
	use App\Game\Gender;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\FextralifeConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\Scrapers\Helpers\FextralifeHelper;
	use App\Scraping\Scrapers\Helpers\S3Helper;
	use App\Scraping\Type;
	use Aws\S3\S3Client;
	use Aws\Sdk;
	use Doctrine\Common\Persistence\ObjectManager;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class FextralifeArmorImageScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		public const S3_BUCKET_PREFIX = 'armor/';

		/**
		 * @var ObjectManager|EntityManagerInterface
		 */
		protected $manager;

		/**
		 * @var S3Client
		 */
		protected $s3Client;

		/**
		 * @var Asset[]
		 */
		protected $imageUrlCache = [];

		/**
		 * FextralifeArmorImageScraper constructor.
		 *
		 * @param FextralifeConfiguration $configuration
		 * @param ObjectManager           $manager
		 * @param Sdk                     $aws
		 */
		public function __construct(FextralifeConfiguration $configuration, ObjectManager $manager, Sdk $aws) {
			parent::__construct($configuration, Type::ARMOR_IMAGES);

			$this->manager = $manager;
			$this->s3Client = $aws->createS3();
		}

		public function scrape(array $context = []): void {
			$qb = $this->manager->createQueryBuilder()->from('App:Armor', 'a');

			$count = $qb
				->select('COUNT(a)')
				->getQuery()
				->getSingleScalarResult();

			$this->progressBar->append($count);

			/** @var Armor[][] $iterator */
			$iterator = $qb
				->select('a')
				->getQuery()
				->iterate();

			foreach ($iterator as $data) {
				$this->process($data[0]);

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		protected function process(Armor $armor): void {
			$uri = $this->getConfiguration()->getBaseUri()
				->withPath('/' . FextralifeHelper::toWikiSlug($armor->getName()));
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$images = (new Crawler($response->getBody()->getContents()))
				->filter('#infobox .wiki_table tr:nth-child(2) img');

			$imageCount = $images->count();

			if ($imageCount === 0)
				throw new \RuntimeException('No images found on ' . $uri);
			else if ($imageCount === 1) {
				$gender = $armor->getAttribute(Attribute::REQUIRED_GENDER);

				if (!$gender) {
					throw new \RuntimeException(sprintf('Only found one image on %s, but %s does not have a required ' .
						'gender', $uri, $armor->getName()));
				}

				$genderMap = [$gender];
			} else
				$genderMap = [Gender::MALE, Gender::FEMALE];

			$assets = $armor->getAssets();

			if (!$assets)
				$armor->setAssets($assets = new ArmorAssets(null, null));

			for ($i = 0; $i < $imageCount; $i++) {
				$imageUrl = $images->eq($i)->attr('src');
				$asset = $this->getAssetByUrl($imageUrl);

				if ($asset) {
					$this->setAsset($assets, $genderMap[$i], $asset);

					continue;
				}

				$image = imagecreatefrompng((string)$this->getConfiguration()->getBaseUri()->withPath($imageUrl));
				imagealphablending($image, true);
				imagesavealpha($image, true);

				$file = tmpfile();

				imagepng($image, $file);
				imagedestroy($image);

				$fileUri = stream_get_meta_data($file)['uri'];

				$primaryHash = hash_file('sha1', $fileUri);
				$secondaryHash = hash_file('md5', $fileUri);
				$bucketKey = S3Helper::toBucketKey(self::S3_BUCKET_PREFIX, $primaryHash, $secondaryHash, '.png');

				$asset = $this->getAssetByHashes($primaryHash, $secondaryHash);

				if (!$asset)
					$asset = new Asset(S3Helper::toBucketUrl($bucketKey), $primaryHash, $secondaryHash);

				$this->setAsset($assets, $genderMap[$i], $asset, $bucketKey, $file);

				$this->imageUrlCache[$imageUrl] = $asset;
			}
		}

		/**
		 * @param string $url
		 *
		 * @return Asset|null
		 */
		protected function getAssetByUrl(string $url): ?Asset {
			return $this->imageUrlCache[$url] ?? null;
		}

		/**
		 * @param ArmorAssets   $assets
		 * @param string        $gender
		 * @param Asset         $asset
		 * @param string|null   $bucketKey
		 * @param resource|null $file
		 *
		 * @return void
		 */
		protected function setAsset(
			ArmorAssets $assets,
			string $gender,
			Asset $asset,
			string $bucketKey = null,
			$file = null
		): void {
			$setter = 'setImage' . ucfirst(strtolower($gender));

			call_user_func([$assets, $setter], $asset);

			if (!$bucketKey || $this->s3Client->doesObjectExist(S3Helper::ASSETS_BUCKET, $bucketKey))
				return;
			else if ($file === null)
				throw new \RuntimeException($bucketKey . ' does not exist, but no file resource was provided');

			$this->s3Client->putObject([
				'Bucket' => S3Helper::ASSETS_BUCKET,
				'Key' => $bucketKey,
				'ContentType' => 'image/png',
				'Body' => $file,
			]);
		}

		/**
		 * @param string $primary
		 * @param string $secondary
		 *
		 * @return Asset|null
		 */
		protected function getAssetByHashes(string $primary, string $secondary): ?Asset {
			return $this->manager->getRepository('App:Asset')->findOneBy([
				'primaryHash' => $primary,
				'secondaryHash' => $secondary,
			]);
		}
	}