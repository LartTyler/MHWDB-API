<?php
	namespace App\Import\Importers;

	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class SkillImporter extends AbstractImporter {
		/**
		 * SkillImporter constructor.
		 */
		public function __construct() {
			parent::__construct(Skill::class);
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function import(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Skill))
				throw $this->createCannotImportException();

			$entity
				->setName($data->name)
				->setSlug($data->slug)
				->setDescription($data->description);

			$entity->getRanks()->clear();

			foreach ($data->ranks as $definition) {
				$rank = new SkillRank($entity, $definition->level, $definition->description);

				$rank
					->setModifiers((array)$definition->modifiers)
					->setSlug($definition->slug);

				$entity->getRanks()->add($rank);
			}
		}

		/**
		 * @param int    $id
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(?int $id, object $data): EntityInterface {
			$skill = new Skill($data->name);
			$skill->setId($id);

			$this->import($skill, $data);

			return $skill;
		}
	}