<?php
	namespace App\Scraping\Scrapers\Helpers;

	final class S3Helper {
		public const ASSETS_BUCKET = 'assets.mhw-db.com';

		/**
		 * S3Helper constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param string $prefix
		 * @param string $primaryHash
		 * @param string $secondaryHash
		 * @param string $extension
		 *
		 * @return string
		 */
		public static function toBucketKey(
			string $prefix,
			string $primaryHash,
			string $secondaryHash,
			?string $extension
		): string {
			$key = sprintf('%s/%s.%s', trim($prefix, '/'), $primaryHash, $secondaryHash);

			if ($extension)
				$key .= '.' . ltrim($extension, '.');

			return $key;
		}

		/**
		 * @param string $key
		 * @param string $bucket
		 *
		 * @return string
		 */
		public static function toBucketUrl(string $key, string $bucket = self::ASSETS_BUCKET) {
			return sprintf('https://%s/%s', $bucket, $key);
		}
	}