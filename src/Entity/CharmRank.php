<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity(repositoryClass="App\Repository\CharmRankRepository")
	 * @ORM\Table(name="charm_ranks")
	 *
	 * Class CharmRank
	 *
	 * @package App\Entity
	 */
	class CharmRank implements EntityInterface, LengthCachingEntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Charm", inversedBy="ranks")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Charm
		 */
		private $charm;

		/**
		 * @Assert\NotBlank()
		 *
		 * @ORM\Column(type="string", length=64, unique=true)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Range(min=1)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $level;

		/**
		 * @ORM\ManyToMany(targetEntity="App\Entity\SkillRank")
		 * @ORM\JoinTable(name="charm_ranks_skill_ranks")
		 *
		 * @var Collection|Selectable|SkillRank[]
		 */
		private $skills;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Range(min=1)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $rarity = 1;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToOne(targetEntity="App\Entity\CharmRankCraftingInfo", orphanRemoval=true, cascade={"all"})
		 *
		 * @var CharmRankCraftingInfo|null
		 */
		private $crafting = null;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "skills.length"
		 */
		private $skillsLength = 0;

		/**
		 * CharmRank constructor.
		 *
		 * @param Charm  $charm
		 * @param string $name
		 * @param int    $level
		 */
		public function __construct(Charm $charm, string $name, int $level) {
			$this->charm = $charm;
			$this->name = $name;
			$this->level = $level;
			$this->skills = new ArrayCollection();
		}

		/**
		 * @return Charm
		 */
		public function getCharm(): Charm {
			return $this->charm;
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
		public function getLevel(): int {
			return $this->level;
		}

		/**
		 * @param int $level
		 *
		 * @return $this
		 */
		public function setLevel(int $level) {
			$this->level = $level;

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
		 * @return SkillRank[]|Collection|Selectable
		 */
		public function getSkills() {
			return $this->skills;
		}

		/**
		 * @return CharmRankCraftingInfo|null
		 */
		public function getCrafting(): ?CharmRankCraftingInfo {
			return $this->crafting;
		}

		/**
		 * @param CharmRankCraftingInfo $crafting
		 *
		 * @return $this
		 */
		public function setCrafting(CharmRankCraftingInfo $crafting) {
			$this->crafting = $crafting;

			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->skillsLength = $this->skills->count();
		}
	}