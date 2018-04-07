<?php
	namespace App\Scraping\Scrapers\Helpers;

	use Symfony\Component\DomCrawler\Crawler;

	final class KiranicoHelper {
		/**
		 * @param Crawler $slotNodes
		 *
		 * @return array
		 */
		public static function getSlots(Crawler $slotNodes): array {
			$slots = [];

			for ($i = 0, $ii = $slotNodes->count(); $i < $ii; $i++) {
				if (!preg_match('/zmdi-n-(\d+)-square/', $slotNodes->eq($i)->attr('class'), $matches))
					continue;

				$key = 'slotsRank' . $matches[1];

				if (!isset($slots[$key]))
					$slots[$key] = 0;

				++$slots[$key];
			}

			return $slots;
		}

		/**
		 * @param string $name
		 * @param string $type
		 *
		 * @return string
		 */
		public static function fixWeaponName(string $name, string $type): string {
			return str_replace([
				'Berseker',
				'Gnshing',
				'Water Golum',
				'Supermacy',
				'Hachets',
				'Jyura Blaster',
				'Rider',
				'Commision',
			], [
				'Berserker',
				'Gnashing',
				'Water Golem',
				'Supremacy',
				'Hatchets',
				'Jyura Buster',
				'Raider',
				'Commission',
			], $name);
		}

		/**
		 * KiranicoHelper constructor.
		 */
		private function __construct() {
		}
	}