<?php
	namespace App\Contrib\Transformers;

	use App\Entity\Armor;
	use App\Entity\ArmorCraftingInfo;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSlot;
	use App\Game\Element;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\EntityTransformerException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\IntegrityException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;
	use DaybreakStudios\Utility\EntityTransformers\Utility\ObjectUtil;

	class ArmorTransformer extends BaseTransformer {
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
					'rank',
					'rarity',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new Armor($data->name, $data->type, $data->rank, $data->rarity);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function doDelete(EntityInterface $entity): void {
			if (!($entity instanceof Armor))
				throw EntityTransformerException::subjectNotSupported($entity);

			if ($set = $entity->getArmorSet())
				$set->getPieces()->removeElement($entity);
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function doUpdate(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Armor))
				throw EntityTransformerException::subjectNotSupported($entity);

			if (ObjectUtil::isset($data, 'name'))
				$entity->setName($data->name);

			if (ObjectUtil::isset($data, 'type'))
				$entity->setType($data->type);

			if (ObjectUtil::isset($data, 'rank'))
				$entity->setRank($data->rank);

			if (ObjectUtil::isset($data, 'rarity'))
				$entity->setRarity($data->rarity);

			if (ObjectUtil::isset($data, 'resistances')) {
				$resistances = $entity->getResistances();
				$definition = $data->resistances;

				foreach (Element::DAMAGE as $element) {
					if (!ObjectUtil::isset($definition, $element))
						continue;

					call_user_func([$resistances, 'set' . ucfirst($element)], $definition->{$element});
				}
			}

			if (ObjectUtil::isset($data, 'defense')) {
				$defense = $entity->getDefense();
				$definition = $data->defense;

				if (ObjectUtil::isset($definition, 'base'))
					$defense->setBase($definition->base);

				if (ObjectUtil::isset($definition, 'max'))
					$defense->setMax($definition->max);

				if (ObjectUtil::isset($definition, 'augmented'))
					$defense->setAugmented($definition->augmented);
			}

			if (ObjectUtil::isset($data, 'skills'))
				$this->populateFromSimpleSkillsArray('skills', $entity->getSkills(), $data->skills);

			if (ObjectUtil::isset($data, 'slots')) {
				$entity->getSlots()->clear();

				foreach ($data->slots as $index => $definition) {
					if (!ObjectUtil::isset($definition, 'rank'))
						throw ValidationException::missingFields(['slots[' . $index . '].rank']);

					$entity->getSlots()->add(new ArmorSlot($entity, $definition->rank));
				}
			}

			if (ObjectUtil::isset($data, 'armorSet')) {
				/** @var ArmorSet|null $armorSet */
				if ($data->armorSet) {
					$armorSet = $this->entityManager->getRepository(ArmorSet::class)->find($data->armorSet);

					if (!$armorSet)
						throw IntegrityException::missingReference('armorSet', 'ArmorSet');
				} else
					$armorSet = null;

				if ($entity->getArmorSet() !== $armorSet) {
					if ($entity->getArmorSet())
						$entity->getArmorSet()->getPieces()->removeElement($entity);

					$entity->setArmorSet($armorSet);

					if ($armorSet)
						$armorSet->getPieces()->add($entity);
				}
			}

			if (ObjectUtil::isset($data, 'crafting')) {
				if (!ObjectUtil::isset($data->crafting, 'materials'))
					throw ValidationException::missingFields(['materials']);

				$crafting = $entity->getCrafting();

				if (!$crafting)
					$entity->setCrafting($crafting = new ArmorCraftingInfo());

				$this->populateFromSimpleCostArray(
					'crafting.materials',
					$crafting->getMaterials(),
					$data->crafting->materials
				);
			}

			if (ObjectUtil::isset($data, 'assets'))
				throw ValidationException::fieldNotSupported('assets');
		}
	}