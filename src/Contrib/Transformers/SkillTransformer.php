<?php
	namespace App\Contrib\Transformers;

	use App\Entity\AilmentProtection;
	use App\Entity\Armor;
	use App\Entity\ArmorSetBonus;
	use App\Entity\ArmorSetBonusRank;
	use App\Entity\Charm;
	use App\Entity\CharmRank;
	use App\Entity\Decoration;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\EntityTransformerException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\IntegrityException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;
	use DaybreakStudios\Utility\EntityTransformers\Utility\ObjectUtil;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;

	class SkillTransformer extends BaseTransformer {
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
					'description',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new Skill($data->name, $data->description);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function doDelete(EntityInterface $entity): void {
			if (!($entity instanceof Skill))
				throw EntityTransformerException::subjectNotSupported($entity);

			$results = $this->entityManager->getRepository(AilmentProtection::class)->findBySkill($entity);

			foreach ($results as $result)
				$result->getSkills()->removeElement($entity);

			$results = $this->entityManager->getRepository(Armor::class)->findBySkill($entity);

			foreach ($results as $result)
				$this->removeFromRanksCollection($result->getSkills(), $entity);

			$results = $this->entityManager->getRepository(ArmorSetBonusRank::class)->findBySkill($entity);

			foreach ($results as $result)
				$this->entityManager->remove($result);

			$results = $this->entityManager->getRepository(CharmRank::class)->findBySkill($entity);

			foreach ($results as $result)
				$this->removeFromRanksCollection($result->getSkills(), $entity);

			$results = $this->entityManager->getRepository(Decoration::class)->findBySkill($entity);

			foreach ($results as $result)
				$this->removeFromRanksCollection($result->getSkills(), $entity);
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function doUpdate(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Skill))
				throw EntityTransformerException::subjectNotSupported($entity);

			if (ObjectUtil::isset($data, 'name'))
				$entity->setName($data->name);

			if (ObjectUtil::isset($data, 'description'))
				$entity->setDescription($data->description);

			if (ObjectUtil::isset($data, 'ranks')) {
				$levels = [];

				foreach ($data->ranks as $index => $definition) {
					$missing = ObjectUtil::getMissingProperties(
						$definition,
						[
							'level',
							'description',
						]
					);

					if ($missing)
						throw ValidationException::missingNestedFields('ranks', $index, $missing);

					$levels[] = $definition->level;

					$rank = $entity->getRank($definition->level);

					if (!$rank) {
						$rank = new SkillRank($entity, $definition->level, $definition->description);

						$entity->getRanks()->add($rank);
					} else
						$rank->setDescription($definition->description);

					if (ObjectUtil::isset($definition, 'modifiers'))
						$rank->setModifiers((array)$definition->modifiers);
				}

				if ($levels) {
					/** @var SkillRank[] $removed */
					$removed = $entity->getRanks()->matching(
						Criteria::create()
							->where(Criteria::expr()->notIn('level', $levels))
					);
				} else
					$removed = $entity->getRanks();

				foreach ($removed as $rank) {
					$usage = $this->getUsage($rank);

					if ($usage) {
						$objectName = $usage[0];

						if ($objectName === ArmorSetBonusRank::class)
							$objectName = ArmorSetBonus::class;
						else if ($objectName === CharmRank::class)
							$objectName = Charm::class;

						$objectName = substr($objectName, strrpos($objectName, '\\') + 1);

						throw new IntegrityException(
							sprintf(
								'Your request would delete rank %d of this skill, but it\'s currently referenced by ' .
								'%d other %s object(s). Remove those references, then try again.',
								$rank->getLevel(),
								$usage[1],
								$objectName
							)
						);
					}

					$entity->getRanks()->removeElement($rank);
				}
			}
		}

		/**
		 * @param Collection|Selectable $collection
		 * @param Skill                 $skill
		 *
		 * @return void
		 */
		protected function removeFromRanksCollection(Collection $collection, Skill $skill): void {
			$matched = $collection->matching(
				Criteria::create()
					->where(Criteria::expr()->eq('skill', $skill))
			);

			foreach ($matched as $match)
				$collection->removeElement($match);
		}

		/**
		 * Returns an array with usage information for the rank. Index 0 will be the fully-qualified class name of the
		 * entity, and index 1 will be the number of objects referencing the rank.
		 *
		 * If this method returns `null`, no usages were found for the rank.
		 *
		 * @param SkillRank $rank
		 *
		 * @return array|null
		 */
		protected function getUsage(SkillRank $rank): ?array {
			if ($count = $this->entityManager->getRepository(Armor::class)->countBySkillRank($rank))
				return [Armor::class, $count];
			else if ($count = $this->entityManager->getRepository(ArmorSetBonusRank::class)->countBySkillRank($rank))
				return [ArmorSetBonusRank::class, $count];
			else if ($count = $this->entityManager->getRepository(CharmRank::class)->countBySkillRank($rank))
				return [CharmRank::class, $count];
			else if ($count = $this->entityManager->getRepository(Decoration::class)->countBySkillRank($rank))
				return [Decoration::class, $count];

			return null;
		}
	}