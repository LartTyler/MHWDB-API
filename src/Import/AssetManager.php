<?php
	namespace App\Import;

	use Aws\S3\S3Client;
	use Aws\Sdk;

	class AssetManager {
		/**
		 * @var S3Client
		 */
		protected $s3Client;

		/**
		 * @var string
		 */
		protected $bucket;

		/**
		 * AssetManager constructor.
		 *
		 * @param Sdk    $aws
		 * @param string $bucket
		 */
		public function __construct(Sdk $aws, string $bucket = 'assets.mhw-db.com') {
			$this->s3Client = $aws->createS3();
			$this->bucket = $bucket;
		}

		/**
		 * @param string   $key
		 * @param resource $image
		 * @param string   $contentType
		 *
		 * @return AssetManager
		 */
		public function put(string $key, $image, string $contentType = 'image/png') {
			if (!is_resource($image))
				throw new \InvalidArgumentException('$image must be a resource');

			if ($this->has($key))
				return $this;

			$this->s3Client->putObject([
				'Bucket' => $this->bucket,
				'Key' => $key,
				'ContentType' => $contentType,
				'Body' => $image,
			]);

			return $this;
		}

		/**
		 * @param string $key
		 *
		 * @return void
		 */
		public function delete(string $key) {
			$this->s3Client->deleteObject([
				'Bucket' => $this->bucket,
				'Key' => $key,
			]);
		}

		/**
		 * @param string $uri
		 *
		 * @return void
		 */
		public function deleteUri(string $uri) {
			$host = parse_url($uri, PHP_URL_HOST);

			if ($host !== $this->bucket)
				throw new \InvalidArgumentException('Only assets in ' . $this->bucket . ' may be deleted');

			$this->delete(ltrim(parse_url($uri, PHP_URL_PATH)));
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		public function has(string $key): bool {
			return $this->s3Client->doesObjectExist($this->bucket, $key);
		}
	}