<?php
	namespace App\Scraping\Scrapers\Helpers;

	use App\Game\WeaponType;
	use Symfony\Component\DomCrawler\Crawler;

	final class KiranicoHelper {
		/**
		 * Returns an array of available slot ranks.
		 *
		 * @param Crawler $slotNodes
		 *
		 * @return int[]
		 */
		public static function getSlots(Crawler $slotNodes): array {
			$slots = [];

			for ($i = 0, $ii = $slotNodes->count(); $i < $ii; $i++) {
				if (!preg_match('/zmdi-n-(\d+)-square/', $slotNodes->eq($i)->attr('class'), $matches))
					continue;

				$slots[] = (int)$matches[1];
			}

			return $slots;
		}

		/**
		 * @param string $fullName
		 *
		 * @return array
		 */
		public static function splitNameAndLevel(string $fullName): array {
			preg_match('/^([^\d]+)(?: (\d+))?$/', $fullName, $matches);

			if (sizeof($matches) < 2)
				throw new \RuntimeException($fullName . ' is not a parseable name');

			return [
				$matches[1],
				(int)($matches[2] ?? 1),
			];
		}

		/**
		 * KiranicoHelper constructor.
		 */
		private function __construct() {
		}
	}