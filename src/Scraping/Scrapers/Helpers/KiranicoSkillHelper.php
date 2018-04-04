<?php
	namespace App\Scraping\Scrapers\Helpers;

	use App\Game\Attribute;

	final class KiranicoSkillHelper {
		private const MODIFIER_MATCHERS = [
			'/Attack \\+(\d+%?)/' => Attribute::ATTACK,
			'/Defense \\+(\d+%?)/' => Attribute::DEFENSE,
			'/All Elemental Resistances \\+(\d+%?)/' => Attribute::RES_ALL,
			'/Health \\+(\d+%?)/' => Attribute::HEALTH,
			'/Affinity \\+(\d+%?)/' => Attribute::AFFINITY,
			'/(Fire|Water|Ice|Thunder|Fire) [rR]esistance \\+(\d+%?)/' => [self::class, 'parseElemResModifier'],
			'/(Fire|Water|Ice|Thunder|Fire) [aA]ttack \\+(\d+%?)(?: Bonus: \\+(\d+%?))?/' =>
				[self::class, 'parseElemDamageModifier'],
			'/Weapon [sS]harpness \\+(\d+%?)/' => Attribute::SHARP_BONUS,
		];

		/**
		 * @param string $description
		 *
		 * @return array
		 */
		public static function parseRankDescriptions(string $description): array {
			$modifiers = [];

			foreach (self::MODIFIER_MATCHERS as $regex => $attribute) {
				if (!preg_match($regex, $description, $matches))
					continue;

				// Throw away the full match, we don't need it
				array_shift($matches);

				if (is_string($attribute))
					$modifiers[$attribute] = $matches[0];
				else if (is_callable($attribute))
					$modifiers = array_merge($modifiers, call_user_func_array($attribute, $matches));
				else
					throw new \InvalidArgumentException('Can\'t handle modifier value. Check ' . static::class .
						'::MODIFIER_MATCHES');
			}

			return $modifiers;
		}

		/**
		 * @param string $element
		 * @param string $amount
		 *
		 * @return array
		 */
		public static function parseElemResModifier(string $element, string $amount): array {
			return [
				'resist' . ucfirst($element) => $amount,
			];
		}

		/**
		 * @param string      $element
		 * @param string      $amount
		 * @param null|string $bonus
		 *
		 * @return array
		 */
		public static function parseElemDamageModifier(string $element, string $amount, ?string $bonus = null): array {
			if ($bonus !== null)
				$amount = $bonus . '+' . $amount;

			return [
				'damage' . ucfirst($element) => $amount,
			];
		}

		/**
		 * SkillHelper constructor.
		 */
		private function __construct() {
		}
	}