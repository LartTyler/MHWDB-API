<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;

	class Charm implements EntityInterface, SluggableInterface {
		use EntityTrait;
		use SluggableTrait;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var Collection|Selectable|SkillRank[]
		 */
		private $skills;

		/**
		 * Charm constructor.
		 *
		 * @param string $name
		 */
		public function __construct(string $name) {
			$this->name = $name;
			$this->skills = new ArrayCollection();

			$this->setSlug($name);
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @return SkillRank[]|Collection|Selectable
		 */
		public function getSkills() {
			return $this->skills;
		}
	}