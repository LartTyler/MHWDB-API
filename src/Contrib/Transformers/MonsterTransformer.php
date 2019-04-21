<?php
	namespace App\Contrib\Transformers;

	use App\Entity\Ailment;
	use App\Entity\Item;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\MonsterResistance;
	use App\Entity\MonsterReward;
	use App\Entity\MonsterWeakness;
	use App\Entity\RewardCondition;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\EntityTransformerException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\IntegrityException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;
	use DaybreakStudios\Utility\EntityTransformers\Utility\ObjectUtil;

	class MonsterTransformer extends BaseTransformer {
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
					'species',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new Monster($data->name, $data->type, $data->species);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function doDelete(EntityInterface $entity): void {
			// noop
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function doUpdate(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Monster))
				throw EntityTransformerException::subjectNotSupported($entity);

			if (ObjectUtil::isset($data, 'name'))
				$entity->setName($data->name);

			if (ObjectUtil::isset($data, 'type'))
				$entity->setType($data->type);

			if (ObjectUtil::isset($data, 'species'))
				$entity->setSpecies($data->species);

			if (ObjectUtil::isset($data, 'description'))
				$entity->setDescription($data->description);

			if (ObjectUtil::isset($data, 'elements'))
				$entity->setElements($data->elements);

			if (ObjectUtil::isset($data, 'ailments'))
				$this->populateFromIdArray('ailments', $entity->getAilments(), Ailment::class, $data->ailments);

			if (ObjectUtil::isset($data, 'locations'))
				$this->populateFromIdArray('locations', $entity->getLocations(), Location::class, $data->locations);

			if (ObjectUtil::isset($data, 'resistances')) {
				$entity->getResistances()->clear();

				foreach ($data->resistances as $index => $definition) {
					if (!ObjectUtil::isset($definition, 'element'))
						throw ValidationException::missingFields(['resistances[' . $index . '].element']);

					$resistance = new MonsterResistance($entity, $definition->element);
					$entity->getResistances()->add($resistance);

					if (ObjectUtil::isset($definition, 'condition'))
						$resistance->setCondition($definition->condition);
				}
			}

			if (ObjectUtil::isset($data, 'weaknesses')) {
				$entity->getWeaknesses()->clear();

				foreach ($data->weaknesses as $index => $definition) {
					$missing = ObjectUtil::getMissingProperties(
						$definition,
						[
							'element',
							'stars',
						]
					);

					if ($missing)
						throw ValidationException::missingNestedFields('weaknesses', $index, $missing);

					$weakness = new MonsterWeakness($entity, $definition->element, $definition->stars);
					$entity->getWeaknesses()->add($weakness);

					if (ObjectUtil::isset($definition, 'condition'))
						$weakness->setCondition($definition->condition);
				}
			}

			if (ObjectUtil::isset($data, 'rewards')) {
				$itemIds = [];

				foreach ($data->rewards as $rewardIndex => $rewardDefinition) {
					$missing = ObjectUtil::getMissingProperties(
						$rewardDefinition,
						[
							'item',
							'conditions',
						]
					);

					if ($missing)
						throw ValidationException::missingNestedFields('rewards', $rewardIndex, $missing);

					/** @var Item|null $item */
					$item = $this->entityManager->getRepository(Item::class)->find($rewardDefinition->item);

					if (!$item)
						throw IntegrityException::missingReference('item', 'Item');

					$itemIds[] = $item->getId();
					$reward = $entity->getRewardForItem($item);

					if (!$reward)
						$entity->getRewards()->add($reward = new MonsterReward($entity, $item));

					$reward->getConditions()->clear();

					foreach ($rewardDefinition->conditions as $index => $definition) {
						$missing = ObjectUtil::getMissingProperties(
							$definition,
							[
								'type',
								'rank',
								'stackSize',
								'chance',
							]
						);

						if ($missing) {
							throw ValidationException::missingNestedFields(
								'rewards[' . $rewardIndex . '].conditions',
								$index,
								$missing
							);
						}

						$condition = new RewardCondition(
							$definition->type,
							$definition->rank,
							$definition->stackSize,
							$definition->chance
						);

						$reward->getConditions()->add($condition);

						if (ObjectUtil::isset($definition, 'subtype'))
							$condition->setSubtype($definition->subtype);
					}
				}

				foreach ($entity->getRewards() as $reward) {
					if (!in_array($reward->getItem()->getId(), $itemIds))
						$entity->getRewards()->removeElement($reward);
				}
			}
		}
	}