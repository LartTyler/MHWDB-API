<?php
	namespace App\Import\Importers;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Entity\Decoration;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\EntityManagerInterface;

	class DecorationImporter extends AbstractImporter {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var ContribManager
		 */
		protected $contribManager;

		/**
		 * DecorationImporter constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ContribManager         $contribManager
		 */
		public function __construct(EntityManagerInterface $entityManager, ContribManager $contribManager) {
			parent::__construct(Decoration::class);

			$this->entityManager = $entityManager;
			$this->contribManager = $contribManager;
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function import(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Decoration))
				throw $this->createCannotImportException();

			$entity
				->setName($data->name)
				->setSlug($data->slug)
				->setSlot($data->slot)
				->setRarity($data->rarity);

			$entity->getSkills()->clear();

			$skillGroup = $this->contribManager->getGroup(EntityType::SKILLS);

			foreach ($data->skills as $i => $definition) {
				$skillId = $skillGroup->getTrueId($definition->skill);
				$skill = $this->entityManager->getRepository(Skill::class)->find($skillId);

				if (!$skill)
					throw $this->createMissingReferenceException('skills[' . $i . '].skill', Skill::class, $skillId);

				$skillRank = $skill->getRank($definition->level);

				if (!$skillRank) {
					throw $this->createMissingReferenceException('skills[' . $i . '].level', SkillRank::class,
						$definition->level);
				}

				$entity->getSkills()->add($skillRank);
			}
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(object $data): EntityInterface {
			$decoration = new Decoration($data->name, $data->slot, $data->rarity);

			$this->import($decoration, $data);

			return $decoration;
		}
	}