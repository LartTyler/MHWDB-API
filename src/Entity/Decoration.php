<?php
	namespace App\Entity;

	use App\Utility\StringUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;

	class Decoration implements EntityInterface, SluggableInterface, LengthCachingEntityInterface {
		use EntityTrait;
		use SluggableTrait;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var int
		 */
		private $slot;

		/**
		 * @var int
		 */
		private $rarity;

		/**
		 * @var Collection|Selectable|SkillRank[]
		 */
		private $skills;

		/**
		 * @var int
		 * @internal Used to allow API queries against "skills.length"
		 */
		private $skillsLength = 0;

		/**
		 * Decoration constructor.
		 *
		 * @param string $name
		 * @param int    $slot
		 * @param int    $rarity
		 */
		public function __construct(string $name, int $slot, int $rarity) {
			$this->name = $name;
			$this->slot = $slot;
			$this->rarity = $rarity;
			$this->skills = new ArrayCollection();

			$this->setSlug(StringUtil::toSlug($name));
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @return int
		 */
		public function getRarity(): int {
			return $this->rarity;
		}

		/**
		 * @param int $rarity
		 *
		 * @return $this
		 */
		public function setRarity(int $rarity) {
			$this->rarity = $rarity;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getSlot(): int {
			return $this->slot;
		}

		/**
		 * @param int $slot
		 *
		 * @return $this
		 */
		public function setSlot(int $slot) {
			$this->slot = $slot;

			return $this;
		}

		/**
		 * @return SkillRank[]|Collection|Selectable
		 */
		public function getSkills() {
			return $this->skills;
		}

		/**
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->skillsLength = $this->skills->count();
		}
	}