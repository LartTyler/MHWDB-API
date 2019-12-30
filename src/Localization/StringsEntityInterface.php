<?php
	namespace App\Localization;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	interface StringsEntityInterface extends EntityInterface {
		/**
		 * @return string
		 * @see LanguageTag
		 */
		public function getLanguage(): string;
	}