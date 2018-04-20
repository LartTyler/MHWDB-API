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
		 * @param string $name
		 * @param string $type
		 *
		 * @return string
		 */
		public static function fixWeaponName(string $name, string $type): string {
			$name = str_replace([
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

			if ($type === WeaponType::BOW) {
				if ($name === 'Villainous Bow')
					$name = 'Villainous Brace';
			}

			return $name;
		}

		/**
		 * KiranicoHelper constructor.
		 */
		private function __construct() {
		}
	}