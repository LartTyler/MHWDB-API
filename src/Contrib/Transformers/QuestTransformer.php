<?php
	namespace App\Contrib\Transformers;

	use App\Entity\EndemicLife;
	use App\Entity\Item;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\MonsterQuestTarget;
	use App\Entity\Quest;
	use App\Entity\QuestReward;
	use App\Entity\RewardCondition;
	use App\Entity\Strings\QuestStrings;
	use App\Entity\WorldEvent;
	use App\Game\Quest\DeliveryType;
	use App\Game\Quest\QuestObjective;
	use App\Localization\L10nUtil;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\IntegrityException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;

	class QuestTransformer extends BaseTransformer {
		/**
		 * {@inheritdoc}
		 */
		public function doCreate(object $data): EntityInterface {
			$missing = ObjectUtil::getMissingProperties(
				$data,
				[
					'location',
					'objective',
					'type',
					'rank',
					'stars',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			/** @var Location|null $location */
			$location = $this->entityManager->find(Location::class, $data->location);

			if (!$location)
				throw IntegrityException::missingReference('location', 'Location');

			$quest = new Quest($location, $data->objective, $data->type, $data->rank, $data->stars);

			// Additional validation, based on the provided objective type. Also gives us a chance to set values that
			// should never be changed during a call to doUpdate().
			switch ($data->objective) {
				case QuestObjective::GATHER:
					$missing = ObjectUtil::getMissingProperties(
						$data,
						[
							'item',
							'amount',
						]
					);

					if ($missing)
						throw ValidationException::missingFields($missing);

					break;

				case QuestObjective::DELIVER:
					$type = $data->deliveryType ?? null;
					$required = [
						'deliveryType',
						'amount',
					];

					if ($type === DeliveryType::ENDEMIC_LIFE)
						$required[] = 'endemicLife';
					else if ($type === DeliveryType::OBJECT)
						$required[] = 'objectName';
					else
						throw ValidationException::invalidFieldType('deliveryType', 'quest delivery type');

					$missing = ObjectUtil::getMissingProperties($data, $required);

					if ($missing)
						throw ValidationException::missingFields($missing);

					$quest->setDeliveryType($data->deliveryType);

					break;

				case QuestObjective::HUNT:
				case QuestObjective::CAPTURE:
				case QuestObjective::SLAY:
					if (!isset($data->targets))
						throw ValidationException::missingFields(['targets']);

					break;

				default:
					throw ValidationException::invalidFieldType('objective', 'quest objective type');
			}

			return $quest;
		}

		/**
		 * {@inheritdoc}
		 */
		public function doUpdate(EntityInterface $entity, object $data): void {
			assert($entity instanceof Quest);

			if (isset($data->name))
				$this->getStrings($entity)->setName($data->name);

			if (isset($data->description))
				$this->getStrings($entity)->setDescription($data->description);

			if (isset($data->type))
				$entity->setType($data->type);

			if (isset($data->rank))
				$entity->setRank($data->rank);

			if (isset($data->stars))
				$entity->setStars($data->stars);

			if (isset($data->timeLimit))
				$entity->setTimeLimit($data->timeLimit);

			if (isset($data->maxHunters))
				$entity->setMaxHunters($data->maxHunters);

			if (isset($data->maxFaints))
				$entity->setMaxFaints($data->maxFaints);

			if (isset($data->location)) {
				/** @var Location|null $location */
				$location = $this->entityManager->find(Location::class, $data->location);

				if (!$location)
					throw IntegrityException::missingReference('location', 'Location');

				$entity->setLocation($location);
			}

			if (isset($data->rewards)) {
				/** @var int[] $itemIds */
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
					$item = $this->entityManager->find(Item::class, $rewardDefinition->item);

					if (!$item)
						throw IntegrityException::missingReference('rewards[' . $rewardIndex . '].item', 'Item');

					$itemIds[] = $item->getId();
					$reward = $entity->getRewardForItem($item);

					if (!$reward)
						$entity->getRewards()->add($reward = new QuestReward($entity, $item));

					$reward->getConditions()->clear();

					foreach ($rewardDefinition->conditions as $index => $definition) {
						$missing = ObjectUtil::getMissingProperties(
							$definition,
							[
								'type',
								'rank',
								'quantity',
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

						$reward->getConditions()->add(
							$condition = new RewardCondition(
								$definition->type,
								$definition->rank,
								$definition->quantity,
								$definition->chance
							)
						);

						if (ObjectUtil::isset($definition, 'subtype')) {
							$subtype = $definition->subtype;

							if (is_string($subtype))
								$subtype = strtolower($subtype);

							$condition->setSubtype($subtype);
						}
					}

					foreach ($entity->getRewards() as $reward) {
						if (!in_array($reward->getItem()->getId(), $itemIds))
							$entity->getRewards()->removeElement($reward);
					}
				}
			}

			switch ($entity->getObjective()) {
				case QuestObjective::GATHER:
					if (isset($data->amount))
						$entity->setAmount($data->amount);

					if (isset($data->item)) {
						/** @var Item|null $item */
						$item = $this->entityManager->find(Item::class, $data->item);

						if (!$item)
							throw IntegrityException::missingReference('item', 'Item');

						$entity->setItem($item);
					}

					break;

				case QuestObjective::DELIVER:
					if (isset($data->amount))
						$entity->setAmount($data->amount);

					if ($entity->getDeliveryType() === DeliveryType::ENDEMIC_LIFE && isset($data->endemicLife)) {
						/** @var EndemicLife|null $endemicLife */
						$endemicLife = $this->entityManager->find(EndemicLife::class, $data->endemicLife);

						if (!$endemicLife)
							throw IntegrityException::missingReference('endemicLife', 'Endemic Life');

						$entity->setEndemicLife($endemicLife);
					} else if ($entity->getDeliveryType() === DeliveryType::OBJECT && isset($data->objectName))
						$this->getStrings($entity)->setObjectName(strtolower($data->objectName));

					break;

				case QuestObjective::HUNT:
				case QuestObjective::CAPTURE:
				case QuestObjective::SLAY:
					if (isset($data->targets)) {
						$entity->getTargets()->clear();

						foreach ($data->targets as $rewardIndex => $definition) {
							$missing = ObjectUtil::getMissingProperties(
								$definition,
								[
									'amount',
									'monster',
								]
							);

							if ($missing)
								throw ValidationException::missingNestedFields('targets', $rewardIndex, $missing);

							/** @var Monster|null $monster */
							$monster = $this->entityManager->find(Monster::class, $definition->monster);

							if (!$monster) {
								throw IntegrityException::missingReference(
									'targets[' . $rewardIndex . '].monster',
									'Monster'
								);
							}

							$entity->getTargets()->add(new MonsterQuestTarget($entity, $monster, $definition->amount));
						}
					}

					break;
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function doDelete(EntityInterface $entity): void {
			assert($entity instanceof Quest);

			// TODO Remove events related to deleted quest
		}

		/**
		 * @param Quest $quest
		 *
		 * @return QuestStrings
		 */
		protected function getStrings(Quest $quest): QuestStrings {
			$strings = L10nUtil::findOrCreateStrings($this->getCurrentLocale(), $quest);
			assert($strings instanceof QuestStrings);

			return $strings;
		}
	}