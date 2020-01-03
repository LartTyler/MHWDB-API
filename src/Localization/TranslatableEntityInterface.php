<?php
	namespace App\Localization;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;

	interface TranslatableEntityInterface {
		/**
		 * @return Collection|Selectable|EntityInterface[]
		 */
		public function getStrings(): Collection;

		/**
		 * @param string $language
		 *
		 * @return EntityInterface
		 * @see LanguageTag
		 */
		public function addStrings(string $language): EntityInterface;
	}