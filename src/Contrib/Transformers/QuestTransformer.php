<?php
	namespace App\Contrib\Transformers;

	use App\Entity\EndemicLife;
	use App\Entity\Item;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\Quest;
	use App\Entity\Quests\AbstractMonsterTargetQuest;
	use App\Entity\Quests\CaptureQuest;
	use App\Entity\Quests\DeliveryQuest;
	use App\Entity\Quests\DeliveryQuestEndemicLifeTarget;
	use App\Entity\Quests\DeliveryQuestObjectTarget;
	use App\Entity\Quests\EndemicLifeDeliveryQuest;
	use App\Entity\Quests\GatherQuest;
	use App\Entity\Quests\HuntQuest;
	use App\Entity\Quests\MonsterQuest;
	use App\Entity\Quests\MonsterQuestTarget;
	use App\Entity\Quests\ObjectDeliveryQuest;
	use App\Entity\Quests\SlayQuest;
	use App\Entity\Strings\QuestStrings;
	use App\Game\Quest\DeliveryType;
	use App\Game\Quest\QuestSubject;
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
					'subject',
					'location',
					'type',
					'rank',
					'stars',
					'name',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			/** @var Location|null $location */
			$location = $this->entityManager->find(Location::class, $data->location);

			if (!$location)
				throw IntegrityException::missingReference('location', 'Location');

			if ($data->subject === QuestSubject::ITEM) {
				$missing = ObjectUtil::getMissingProperties(
					$data,
					[
						'item',
						'amount',
					]
				);

				// We only need to validate that the extra fields exist. Setting the value is deferred to doUpdate().
				if ($missing)
					throw ValidationException::missingFields($missing);

				return new GatherQuest($location, $data->type, $data->rank, $data->stars);
			} else if ($data->subject === QuestSubject::ENTITY) {
				if (!isset($data->target))
					throw ValidationException::missingFields(['target']);

				return new DeliveryQuest($location, $data->type, $data->rank, $data->stars);
			} else if ($data->subject === QuestSubject::MONSTER) {
				if (!isset($data->targets))
					throw ValidationException::missingFields(['targets']);

				return new MonsterQuest($location, $data->objective, $data->type, $data->rank, $data->stars);
			}

			throw ValidationException::invalidFieldType('subject', 'quest subject type');
		}

		/**
		 * {@inheritdoc}
		 */
		public function doUpdate(EntityInterface $entity, object $data): void {
			assert($entity instanceof Quest);

			if (ObjectUtil::isset($data, 'name'))
				$this->getStrings($entity)->setName($data->name);

			if (ObjectUtil::isset($data, 'description'))
				$this->getStrings($entity)->setDescription($data->description);

			if (ObjectUtil::isset($data, 'type'))
				$entity->setType($data->type);

			if (ObjectUtil::isset($data, 'rank'))
				$entity->setRank($data->rank);

			if (ObjectUtil::isset($data, 'stars'))
				$entity->setStars($data->stars);

			if (ObjectUtil::isset($data, 'timeLimit'))
				$entity->setTimeLimit($data->timeLimit);

			if (ObjectUtil::isset($data, 'maxHunters'))
				$entity->setMaxHunters($data->maxHunters);

			if (ObjectUtil::isset($data, 'maxFaints'))
				$entity->setMaxFaints($data->maxFaints);

			if (ObjectUtil::isset($data, 'location')) {
				/** @var Location|null $location */
				$location = $this->entityManager->find(Location::class, $data->location);

				if (!$location)
					throw IntegrityException::missingReference('location', 'Location');

				$entity->setLocation($location);
			}

			if ($entity instanceof GatherQuest) {
				if (ObjectUtil::isset($data, 'amount'))
					$entity->setAmount($data->amount);

				if (ObjectUtil::isset($data, 'item')) {
					/** @var Item|null $item */
					$item = $this->entityManager->find(Item::class, $data->item);

					if (!$item)
						throw IntegrityException::missingReference('item', 'Item');

					$entity->setItem($item);
				}
			} else if ($entity instanceof DeliveryQuest) {
				if (ObjectUtil::isset($data, 'amount'))
					$entity->setAmount($data->amount);

				if (ObjectUtil::isset($data, 'target')) {
					$missing = ObjectUtil::getMissingProperties(
						$definition = $data->target,
						[
							'amount',
							'deliveryType',
						]
					);

					if ($missing) {
						throw ValidationException::missingFields(
							array_map(
								function(string $item) {
									return 'target.' . $item;
								},
								$missing
							)
						);
					}

					switch ($definition->deliveryType) {
						case DeliveryType::OBJECT:
							if (!isset($definition->objectName))
								throw ValidationException::missingFields(['target.objectName']);

							$target = $entity->getTarget();

							if (!$target || !($target instanceof DeliveryQuestObjectTarget))
								$entity->setTarget(new DeliveryQuestObjectTarget($entity, $definition->amount));

							$this->getStrings($entity)->setObjectName($definition->objectName);

							break;

						case DeliveryType::ENDEMIC_LIFE:
							if (!isset($definition->endemicLife))
								throw ValidationException::missingFields(['target.endemicLife']);

							/** @var EndemicLife|null $endemicLife */
							$endemicLife = $this->entityManager->find(EndemicLife::class, $definition->endemicLife);

							if (!$endemicLife)
								throw IntegrityException::missingReference('target.endemicLife', 'Endemic Life');

							$target = $entity->getTarget();

							if (!$target || !($target instanceof DeliveryQuestEndemicLifeTarget)) {
								$entity->setTarget(
									$target = new DeliveryQuestEndemicLifeTarget($entity, $definition->amount)
								);
							}

							$target->setEndemicLife($endemicLife);

							break;

						default:
							throw ValidationException::invalidFieldType(
								'target.deliveryType',
								'delivery quest target type'
							);
					}
				}
			} else if ($entity instanceof MonsterQuest) {
				if (ObjectUtil::isset($data, 'objective'))
					$entity->setObjective($data->objective);

				if (ObjectUtil::isset($data, 'targets')) {
					$entity->getTargets()->clear();

					foreach ($data->targets as $index => $definition) {
						$missing = ObjectUtil::getMissingProperties(
							$definition,
							[
								'amount',
								'monster',
							]
						);

						if ($missing)
							throw ValidationException::missingNestedFields('targets', $index, $missing);

						/** @var Monster|null $monster */
						$monster = $this->entityManager->find(Monster::class, $definition->monster);

						if (!$monster)
							throw IntegrityException::missingReference('targets[' . $index . '].monster', 'Monster');

						$entity->getTargets()->add(new MonsterQuestTarget($entity, $monster, $definition->amount));
					}
				}
			} else
				throw new \InvalidArgumentException(get_class($entity) . ' is not yet supported');
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