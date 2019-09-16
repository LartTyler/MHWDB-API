<?php
	namespace App\Command;

	use App\Entity\Shelling;
	use App\Entity\Weapon;
	use App\Game\Attribute;
	use App\Game\WeaponType;
	use Doctrine\ORM\QueryBuilder;

	class ExtractShellingTypeAttributeCommand extends AbstractExtractWeaponAttributeCommand {
		/**
		 * {@inheritdoc}
		 */
		protected function addQueryBuilderClauses(QueryBuilder $queryBuilder): void {
			$queryBuilder
				->andWhere('w.type = :type')
				->setParameter('type', WeaponType::GUNLANCE);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function process(Weapon $weapon, bool $deleteAttribute): void {
			$shellingType = $weapon->getAttribute(Attribute::GL_SHELLING_TYPE);

			if (!$shellingType)
				return;

			$parts = explode(' ', $shellingType);

			if (sizeof($parts) !== 2) {
				throw new \RuntimeException(
					sprintf('Cannot parse shelling type "%s" (for Weapon#%d)', $shellingType, $weapon->getId())
				);
			}

			$shelling = new Shelling($weapon, strtolower($parts[0]), (int)substr($parts[1], 2));
			$weapon->setShelling($shelling);

			if ($deleteAttribute)
				$weapon->removeAttribute(Attribute::GL_SHELLING_TYPE);
		}
	}