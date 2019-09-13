<?php
	namespace App\Command;

	use App\Entity\Weapon;
	use App\Game\Attribute;
	use App\Game\WeaponType;
	use Doctrine\ORM\QueryBuilder;
	use Symfony\Component\Validator\ConstraintViolationListInterface;

	class ExtractSpecialAmmoAttributesCommand extends AbstractExtractWeaponAttributeCommand {
		/**
		 * {@inheritdoc}
		 */
		protected static $defaultName = 'app:tools:extract-special-ammo-attributes';

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
			$specialAmmo = $weapon->getAttribute(Attribute::SPECIAL_AMMO);

			if (!$specialAmmo)
				return;

			$weapon->setSpecialAmmo($specialAmmo);

			if ($deleteAttribute)
				$weapon->removeAttribute(Attribute::SPECIAL_AMMO);
		}

		/**
		 * @param Weapon $weapon
		 *
		 * @return ConstraintViolationListInterface
		 */
		protected function validate(Weapon $weapon): ConstraintViolationListInterface {
			return $this->validator->validateProperty($weapon, 'specialAmmo');
		}
	}