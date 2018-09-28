<?php
	namespace App\Contrib\Transformers;

	use App\Contrib\Exceptions\ValidationException;
	use App\Entity\Charm;
	use App\Entity\CharmRank;
	use App\Entity\CharmRankCraftingInfo;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Criteria;

	class CharmTransformer extends AbstractTransformer {
		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function update(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Charm))
				throw $this->createEntityNotSupportedException(get_class($entity));

			if (ObjectUtil::isset($data, 'name'))
				$entity->setName($data->name);

			if (ObjectUtil::isset($data, 'ranks')) {
				$levels = [];

				foreach ($data->ranks as $index => $definition) {
					$pathPrefix = 'ranks[' . $index . ']';

					if (!ObjectUtil::isset($definition, 'level'))
						throw ValidationException::missingFields([$pathPrefix . '.level']);

					$levels[] = $definition->level;

					$charmRank = $entity->getRank($definition->level);

					if (!$charmRank) {
						if (!ObjectUtil::isset($definition, 'name'))
							throw ValidationException::missingFields([$pathPrefix . '.name']);

						$charmRank = new CharmRank($entity, $definition->name, $definition->level);
						$entity->getRanks()->add($charmRank);
					}

					if (ObjectUtil::isset($definition, 'name'))
						$charmRank->setName($definition->name);

					if (ObjectUtil::isset($definition, 'rarity'))
						$charmRank->setRarity($definition->rarity);

					if (ObjectUtil::isset($definition, 'skills')) {
						$this->populateFromSimpleSkillsArray(
							$pathPrefix . '.skills',
							$charmRank->getSkills(),
							$definition->skills
						);
					}

					if (ObjectUtil::isset($definition, 'crafting')) {
						$crafting = new CharmRankCraftingInfo();
						$craftingDefinition = $definition->crafting;

						$charmRank->setCrafting($crafting);

						if (ObjectUtil::isset($craftingDefinition, 'craftable'))
							$crafting->setCraftable($craftingDefinition->craftable);

						if (ObjectUtil::isset($craftingDefinition, 'materials')) {
							$this->populateFromSimpleCostArray(
								$pathPrefix . '.crafting.materials',
								$crafting->getMaterials(),
								$craftingDefinition->materials
							);
						}

						$charmRank->setCrafting($crafting);
					}
				}

				if ($levels) {
					$matching = $entity->getRanks()->matching(
						Criteria::create()
							->where(Criteria::expr()->notIn('level', $levels))
					);

					foreach ($matching as $item)
						$entity->getRanks()->removeElement($item);
				} else
					$entity->getRanks()->clear();
			}
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		protected function doCreate(object $data): EntityInterface {
			$missing = ObjectUtil::getMissingProperties(
				$data,
				[
					'name',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new Charm($data->name);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		protected function doDelete(EntityInterface $entity): void {
			// noop
		}
	}