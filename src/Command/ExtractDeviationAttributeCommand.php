<?php
	namespace App\Command;

	use App\Entity\Weapon;
	use App\Game\Attribute;
	use App\Game\WeaponType;
	use Doctrine\ORM\QueryBuilder;

	class ExtractDeviationAttributeCommand extends AbstractExtractWeaponAttributeCommand {
		/**
		 * {@inheritdoc}
		 */
		protected static $defaultName = 'app:tools:extract-deviation-attributes';

		/**
		 * {@inheritdoc}
		 */
		protected function addQueryBuilderClauses(QueryBuilder $queryBuilder): void {
			$queryBuilder
				->andWhere('w.type IN (:types)')
				->setParameter(
					'types',
					[
						WeaponType::LIGHT_BOWGUN,
						WeaponType::HEAVY_BOWGUN,
					]
				);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function process(Weapon $weapon, bool $deleteAttribute): void {
			$deviation = $weapon->getAttribute(Attribute::DEVIATION);

			if (!$deviation)
				return;

			$weapon->setDeviation($deviation);

			if ($deleteAttribute)
				$weapon->removeAttribute(Attribute::DEVIATION);
		}
	}