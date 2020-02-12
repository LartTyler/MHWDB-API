<?php
	namespace App\Contrib\Transformers;

	use App\Entity\Item;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\Quest;
	use App\Entity\Quests\AbstractMonsterTargetQuest;
	use App\Entity\Quests\CaptureQuest;
	use App\Entity\Quests\GatherQuest;
	use App\Entity\Quests\HuntQuest;
	use App\Entity\Quests\SlayQuest;
	use App\Entity\Strings\QuestStrings;
	use App\Game\Quest\Objective;
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
					'objective',
					'location',
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

			if ($data->objective === Objective::GATHER) {
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
			} else { // All other objective types involve targeting a monster or monsters
				// We only need to validate that the field exists. Setting the value is deferred to doUpdate().
				if (!isset($data->monsters))
					throw ValidationException::missingFields(['monsters']);

				switch ($data->objective) {
					case Objective::CAPTURE:
						return new CaptureQuest($location, $data->type, $data->rank, $data->stars);

					case Objective::HUNT:
						return new HuntQuest($location, $data->type, $data->rank, $data->stars);

					case Objective::SLAY:
						return new SlayQuest($location, $data->type, $data->rank, $data->stars);

					default:
						throw ValidationException::invalidFieldValue('objective', 'unrecognized objective name');
				}
			}
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
				if (ObjectUtil::isset($data, 'item')) {
					/** @var Item|null $item */
					$item = $this->entityManager->find(Item::class, $data->item);

					if (!$item)
						throw IntegrityException::missingReference('item', 'Item');

					$entity->setItem($item);
				}

				if (ObjectUtil::isset($data, 'amount'))
					$entity->setAmount($data->amount);
			} else if ($entity instanceof AbstractMonsterTargetQuest && ObjectUtil::isset($data, 'monsters'))
				$this->populateFromIdArray('monsters', $entity->getMonsters(), Monster::class, $data->monsters);
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