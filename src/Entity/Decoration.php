<?php
	namespace App\Entity;

	use App\Utility\StringUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity(repositoryClass="App\Repository\DecorationRepository")
	 * @ORM\Table(name="decorations")
	 *
	 * Class Decoration
	 *
	 * @package App\Entity
	 */
	class Decoration implements EntityInterface, LengthCachingEntityInterface {
		use EntityTrait;

		/**
		 * @ORM\Column(type="string", length=64, unique=true)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $slot;

		/**
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $rarity;

		/**
		 * @ORM\ManyToMany(targetEntity="App\Entity\SkillRank")
		 * @ORM\JoinTable(name="decorations_skill_ranks")
		 *
		 * @var Collection|Selectable|SkillRank[]
		 */
		private $skills;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 *
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
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function setName(string $name) {
			$this->name = $name;

			return $this;
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