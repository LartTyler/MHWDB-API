<?php

	namespace App\Localization;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Criteria;

	final class L10nUtil {
		/**
		 * @param string                      $language
		 * @param TranslatableEntityInterface $entity
		 *
		 * @return EntityInterface|null
		 */
		public static function findStrings(string $language, TranslatableEntityInterface $entity): ?EntityInterface {
			$criteria = $criteria = Criteria::create()
				->where(Criteria::expr()->eq('language', $language))
				->setMaxResults(1);

			return $entity->getStrings()->matching($criteria)->first() ?: null;
		}

		/**
		 * @param string                      $language
		 * @param TranslatableEntityInterface $entity
		 *
		 * @return EntityInterface
		 */
		public static function findOrCreateStrings(
			string $language,
			TranslatableEntityInterface $entity
		): EntityInterface {
			return self::findStrings($language, $entity) ?? $entity->addStrings($language);
		}
	}