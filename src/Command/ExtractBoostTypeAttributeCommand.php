<?php
	namespace App\Command;

	use App\Entity\Weapon;
	use App\Game\Attribute;
	use App\Game\WeaponType;
	use Doctrine\ORM\QueryBuilder;

	class ExtractBoostTypeAttributeCommand extends AbstractExtractWeaponAttributeCommand {
		/**
		 * {@inheritdoc}
		 */
		protected static $defaultName = 'app:tools:extract-boost-type-attributes';

		/**
		 * {@inheritdoc}
		 */
		protected function addQueryBuilderClauses(QueryBuilder $queryBuilder): void {
			$queryBuilder
				->andWhere('w.type = :type')
				->setParameter('type', WeaponType::INSECT_GLAIVE);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function process(Weapon $weapon, bool $deleteAttribute): void {
			$boostType = $weapon->getAttribute(Attribute::IG_BOOST_TYPE);

			if (!$boostType)
				return;

			$weapon->setBoostType($boostType);

			if ($deleteAttribute)
				$weapon->removeAttribute(Attribute::IG_BOOST_TYPE);
		}
	}