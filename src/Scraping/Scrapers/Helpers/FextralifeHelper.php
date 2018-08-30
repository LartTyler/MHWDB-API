<?php
	namespace App\Scraping\Scrapers\Helpers;

	use App\Game\Ailment;
	use App\Game\Element;
	use App\Utility\StringUtil;

	final class FextralifeHelper {
		public const AILMENT_ALT_NAMES = [
			Ailment::EFFLUVIAL_BUILDUP => 'miasma',
		];

		/**
		 * FextralifeHelper constructor.
		 */
		private function __construct() {
		}

		/**
		 * Changes correct weapon names to names that match what's on the Fextralife wiki.
		 *
		 * @param string $name
		 *
		 * @return string
		 */
		public static function toWikiWeaponName(string $name): string {
			return str_replace([
				'Sworn Rapiers+',
				'Dancing Davul',
				'Princess Panoply+',
			], [
				'Sworn Rapiers +',
				'Dancing Duval',
				'Princess Panoply +',
			], $name);
		}

		/**
		 * @param string $value
		 *
		 * @return string
		 */
		public static function toWikiSlug(string $value): string {
			$value = str_replace([
				'(',
				')',
				'"'
			], '', $value);

			if ($value === 'King Beetle Thorax')
				$value = 'King Beetle Thorax ';
			else if ($value === 'Rath Heart Braces Alpha')
				$value = 'Rath Heart Vambraces Alpha';
			else if ($value === 'Rath Heart Braces Beta')
				$value = 'Rath Heart Vambraces Beta';
			else if ($value === 'Faux Felyne Alpha')
				$value = 'Faux Felyne Alpha Helm';
			else if ($value === 'Diablos Nero Braces Alpha')
				$value = 'Diablos Nero Vambraces Alpha';
			else if ($value === 'Diablos Nero Braces Beta')
				$value = 'Diablos Nero Vambraces Beta';
			else if ($value === 'Kulu-Yaku Head Alpha')
				$value = 'Kulu-Ya-Ku Head Alpha';

			return strtr($value, ' ', '+');
		}

		/**
		 * @param string $path
		 *
		 * @return string
		 */
		public static function fixWikiImageLink(string $path): string {
			return str_replace([
				'horntaur',
				'high_metal_vambraces_alpha_female'
			], [
				'hornetaur',
				'high_metal_braces_alpha_female',
			], $path);
		}

		/**
		 * @param string $text
		 *
		 * @return string[]
		 */
		public static function extractElements(string $text): array {
			$text = self::cleanMultibyteWhitespace(StringUtil::clean(strtolower($text)));

			$output = [];

			foreach (Element::ALL as $element) {
				if (stripos($text, $element) !== false)
					$output[] = $element;
			}

			return $output;
		}

		/**
		 * @param string $text
		 *
		 * @return string[]
		 */
		public static function extractAilments(string $text): array {
			$text = self::cleanMultibyteWhitespace(StringUtil::clean(strtolower($text)));

			// Fix for Gajalaka page referring to paralysis as paralyze
			$text = str_replace('paralyze', 'paralysis', $text);

			$output = [];

			foreach (Ailment::all() as $ailment) {
				if (stripos($text, $ailment) !== false)
					$output[] = $ailment;
				else if ($alt = (self::AILMENT_ALT_NAMES[$ailment] ?? null)) {
					if (stripos($text, $alt) !== false)
						$output[] = $ailment;
				}
			}

			return $output;
		}

		/**
		 * @param string $value
		 *
		 * @return string
		 */
		public static function cleanMultibyteWhitespace(string $value): string {
			return str_replace([
				' '
			], [
				''
			], $value);
		}

		/**
		 * @param string $value
		 *
		 * @return string
		 */
		public static function cleanSpeciesName(string $value): string {
			$value = self::cleanMultibyteWhitespace(StringUtil::clean(strtolower($value)));

			if ($value[strlen($value) - 1] === 's')
				$value = substr($value, 0, -1);

			return $value;
		}

		/**
		 * @param string $value
		 *
		 * @return null|string
		 */
		public static function cleanElementName(string $value): ?string {
			$value = self::cleanMultibyteWhitespace(StringUtil::clean(strtolower($value)));

			if (!$value || in_array($value, ['n/a', 'none', '?', '??', 'physical', 'only']))
				return null;

			return $value;
		}

		/**
		 * @param string $value
		 *
		 * @return string
		 */
		public static function cleanAilmentName(string $value): ?string {
			$value = self::cleanMultibyteWhitespace(StringUtil::clean(strtolower($value)));

			if (!$value || in_array($value, ['n/a', 'none', '?', '??']))
				return null;
			else if ($value === 'paralyze')
				$value = 'paralysis';

			return $value;
		}
	}