<?php
	namespace App\Contrib\Transformers;

	use App\Entity\Ammo;
	use App\Entity\Phial;
	use App\Entity\Weapon;
	use App\Entity\WeaponCraftingInfo;
	use App\Entity\WeaponElement;
	use App\Entity\WeaponSharpness;
	use App\Entity\WeaponSlot;
	use App\Game\Attribute;
	use App\Game\RawDamageMultiplier;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\EntityTransformerException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\IntegrityException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;
	use DaybreakStudios\Utility\EntityTransformers\Utility\ObjectUtil;
	use Doctrine\Common\Collections\Criteria;

	class WeaponTransformer extends BaseTransformer {
		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function doCreate(object $data): EntityInterface {
			$missing = ObjectUtil::getMissingProperties(
				$data,
				[
					'name',
					'type',
					'rarity',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new Weapon($data->name, $data->type, $data->rarity);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function doDelete(EntityInterface $entity): void {
			if (!($entity instanceof Weapon))
				throw EntityTransformerException::subjectNotSupported($entity);

			if ($crafting = $entity->getCrafting()) {
				if (($count = $crafting->getBranches()->count()) > 0) {
					throw new IntegrityException(
						sprintf(
							'This weapon is referenced by %d other weapon(s) as the previous weapon in their ' .
							'crafting tree. Remove those references, then try again.',
							$count
						)
					);
				}

				if ($previous = $crafting->getPrevious())
					$previous->getCrafting()->getBranches()->removeElement($entity);
			}
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function doUpdate(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Weapon))
				throw EntityTransformerException::subjectNotSupported($entity);

			if (ObjectUtil::isset($data, 'name'))
				$entity->setName($data->name);

			if (ObjectUtil::isset($data, 'type'))
				$entity->setType($data->type);

			if (ObjectUtil::isset($data, 'rarity'))
				$entity->setRarity($data->rarity);

			if (ObjectUtil::isset($data, 'attributes'))
				$entity->setAttributes((array)$data->attributes);

			if (ObjectUtil::isset($data, 'elderseal')) {
				$entity->setElderseal($data->elderseal);

				// TODO Preserves BC for 1.15.0, will be removed in 1.17.0
				$entity->setAttribute(Attribute::ELDERSEAL, $data->elderseal);
			}

			if (ObjectUtil::isset($data, 'phial')) {
				if (!$data->phial) {
					$entity->setPhial(null);

					// TODO Preserves BC for 1.15.0, will be removed in 1.17.0
					$entity->removeAttribute(Attribute::PHIAL_TYPE);
				} else {
					if (!isset($data->phial->type))
						throw ValidationException::missingFields(['phial.type']);

					$phial = $entity->getPhial();

					if (!$phial)
						$entity->setPhial($phial = new Phial($entity, $data->phial->type));

					if (isset($data->phial->damage))
						$phial->setDamage($data->phial->damage);
					else
						$phial->setDamage(null);

					// TODO Preserves BC for 1.15.0, will be removed in 1.17.0
					$entity->setAttribute(Attribute::PHIAL_TYPE, trim($phial->getType() . ' ' . $phial->getDamage()));
				}
			}

			if (ObjectUtil::isset($data, 'slots')) {
				$entity->getSlots()->clear();

				foreach ($data->slots as $index => $definition) {
					if (!ObjectUtil::isset($definition, 'rank'))
						throw ValidationException::missingNestedFields('slots', $index, ['rank']);

					$entity->getSlots()->add(new WeaponSlot($entity, $definition->rank));
				}
			}

			if (ObjectUtil::isset($data, 'durability')) {
				$entity->getDurability()->clear();

				foreach ($data->durability as $index => $definition) {
					$missing = ObjectUtil::getMissingProperties(
						$definition,
						[
							'red',
							'orange',
							'yellow',
							'green',
							'blue',
							'white',
						]
					);

					if ($missing)
						throw ValidationException::missingNestedFields('durability', $index, $missing);

					$durability = new WeaponSharpness();
					$durability
						->setRed($definition->red)
						->setOrange($definition->orange)
						->setYellow($definition->yellow)
						->setGreen($definition->green)
						->setBlue($definition->blue)
						->setWhite($definition->white);

					$entity->getDurability()->add($durability);
				}
			}

			if (ObjectUtil::isset($data, 'elements')) {
				$elements = [];

				foreach ($data->elements as $index => $definition) {
					$missing = ObjectUtil::getMissingProperties(
						$definition,
						[
							'type',
							'damage',
						]
					);

					if ($missing)
						throw ValidationException::missingNestedFields('elements', $index, $missing);

					$elements[] = $definition->type;

					$element = $entity->getElement($definition->type);

					if (!$element) {
						$element = new WeaponElement($entity, $definition->type, $definition->damage);

						$entity->getElements()->add($element);
					} else
						$element->setDamage($definition->damage);

					if (ObjectUtil::isset($definition, 'hidden'))
						$element->setHidden($definition->hidden);
				}

				$removed = $entity->getElements()->matching(
					Criteria::create()
						->where(Criteria::expr()->notIn('type', $elements))
				);

				foreach ($removed as $element)
					$entity->getElements()->removeElement($element);
			}

			if (ObjectUtil::isset($data, 'attack')) {
				$attack = $entity->getAttack();
				$definition = $data->attack;

				if (ObjectUtil::isset($definition, 'display')) {
					$attack
						->setDisplay($definition->display)
						->setRaw($definition->display / RawDamageMultiplier::get($entity->getType()));
				}

				if (ObjectUtil::isset($definition, 'raw'))
					$attack->setRaw($definition->raw);
			}

			if (ObjectUtil::isset($data, 'ammo')) {
				$types = [];

				foreach ($data->ammo as $index => $definition) {
					$missing = ObjectUtil::getMissingProperties(
						$definition,
						[
							'type',
							'capacities',
						]
					);

					if ($missing)
						throw ValidationException::missingNestedFields('ammo', $index, $missing);

					$types[] = $definition->type;
					$ammo = $entity->getAmmoByType($definition->type);

					if (!$ammo)
						$entity->getAmmo()->add($ammo = new Ammo($entity, $definition->type));

					$ammo->setCapacities($definition->capacities);
				}

				foreach ($entity->getAmmo() as $ammo) {
					if ($ammo->isEmpty() || !in_array($ammo->getType(), $types))
						$entity->getAmmo()->removeElement($ammo);
				}

				// TODO Preserves BC for 1.15.0, will be removed in 1.17.0
				if ($entity->getAmmo()->count() > 0) {
					$capacities = [];

					foreach ($entity->getAmmo() as $ammo)
						$capacities[$ammo->getType()] = $ammo->getCapacities();

					$entity->setAttribute(Attribute::AMMO_CAPACITIES, $capacities);
				} else
					$entity->removeAttribute(Attribute::AMMO_CAPACITIES);
			}

			if (ObjectUtil::isset($data, 'crafting')) {
				$crafting = $entity->getCrafting();
				$definition = $data->crafting;

				if (!$crafting) {
					if (!ObjectUtil::isset($definition, 'craftable'))
						throw ValidationException::missingFields(['crafting.craftable']);

					$entity->setCrafting($crafting = new WeaponCraftingInfo($definition->craftable));
				} else if (ObjectUtil::isset($definition, 'craftable'))
					$crafting->setCraftable($definition->craftable);

				if (ObjectUtil::isset($definition, 'craftingMaterials')) {
					$this->populateFromSimpleCostArray(
						'crafting.craftingMaterials',
						$crafting->getCraftingMaterials(),
						$definition->craftingMaterials
					);
				}

				if (!$crafting->isCraftable() && $crafting->getCraftingMaterials()->count() > 0) {
					throw new ValidationException(
						'You specified that the weapon is not craftable, but provided values in ' .
						'[crafting.craftingMaterials]. A weapon cannot have a crafting cost if it is not craftable.'
					);
				} else if ($crafting->isCraftable() && $crafting->getCraftingMaterials()->count() === 0) {
					throw new ValidationException(
						'You specified that the weapon is craftable, but did not provide any values in ' .
						'[crafting.craftingMaterials]. A weapon must have a crafting cost if it is craftable.'
					);
				}

				if (ObjectUtil::isset($definition, 'upgradeMaterials')) {
					$this->populateFromSimpleCostArray(
						'crafting.upgradeMaterials',
						$crafting->getUpgradeMaterials(),
						$definition->upgradeMaterials
					);
				}

				if (ObjectUtil::isset($definition, 'previous')) {
					/** @var Weapon|null $previous */
					$previous = $this->entityManager->getRepository(Weapon::class)->find($definition->previous);

					if (!$previous)
						throw IntegrityException::missingReference('crafting.previous', 'Weapon');
					else if (!$previous->getCrafting()) {
						throw new IntegrityException(
							'The previous weapon in the crafting tree that you specified has no associated crafting ' .
							'data. Fix that, then try again.'
						);
					}

					if ($crafting->getPrevious())
						$crafting->getPrevious()->getCrafting()->getBranches()->removeElement($entity);

					$crafting->setPrevious($previous);

					if (!$previous->getCrafting()->getBranches()->contains($entity))
						$previous->getCrafting()->getBranches()->add($entity);
				}

				if ($crafting->getPrevious() && $crafting->getUpgradeMaterials()->count() === 0) {
					throw new ValidationException(
						'You specified that the weapon has a previous weapon in it\'s crafting tree, but did not ' .
						'provide values in [crafting.upgradeMaterials]. A weapon must have an upgrade cost if it has ' .
						'a previous weapon in it\'s crafting tree.'
					);
				}
			}

			if (ObjectUtil::isset($data, 'assets'))
				throw ValidationException::fieldNotSupported('assets');
		}
	}