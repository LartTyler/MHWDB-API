<?php
	namespace App\Command;

	use App\Entity\Shelling;
	use App\Entity\Weapon;
	use App\Game\Attribute;
	use App\Game\WeaponType;
	use Doctrine\ORM\QueryBuilder;
	use Symfony\Component\Validator\ConstraintViolationListInterface;

	class ExtractShellingTypeAttributeCommand extends AbstractExtractWeaponAttributeCommand {
		protected static $defaultName = 'app:tools:extract-shelling-type-attributes';

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

			$type = strtolower($parts[0]);
			$level = (int)substr($parts[1], 2);

			if ($shelling = $weapon->getShelling()) {
				$shelling
					->setLevel($level)
					->setType($type);
			} else {
				$shelling = new Shelling($weapon, strtolower($parts[0]), (int)substr($parts[1], 2));
				$weapon->setShelling($shelling);
			}

			if ($deleteAttribute)
				$weapon->removeAttribute(Attribute::GL_SHELLING_TYPE);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function validate(Weapon $weapon): ?ConstraintViolationListInterface {
			return $this->validator->validateProperty($weapon, 'shelling');
		}
	}
