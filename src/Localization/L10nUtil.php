<?php

	namespace App\Localization;

	use App\Utility\NullObject;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;

	final class L10nUtil {
		/**
		 * @param string     $language
		 * @param Selectable $strings
		 *
		 * @return EntityInterface|NullObject
		 */
		public static function findStringsForTag(string $language, Selectable $strings) {
			$criteria = Criteria::create()
				->where(Criteria::expr()->eq('language', $language))
				->setMaxResults(1);

			return NullObject::of($strings->matching($criteria)->first() ?: null);
		}
	}