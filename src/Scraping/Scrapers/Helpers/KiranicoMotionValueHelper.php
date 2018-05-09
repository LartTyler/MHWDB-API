<?php
	namespace App\Scraping\Scrapers\Helpers;

	use App\Game\DamageType;

	final class KiranicoMotionValueHelper {
		/**
		 * @param string $type
		 *
		 * @return string|null
		 */
		public static function cleanDamageType(string $type): ?string {
			$type = strtolower($type);

			if (!$type)
				return null;

			if ($type === 'shot')
				$type = DamageType::PROJECTILE;

			return $type;
		}

		/**
		 * KiranicoMotionValueHelper constructor.
		 */
		private function __construct() {
		}
	}