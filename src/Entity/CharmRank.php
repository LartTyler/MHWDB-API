<?php
	namespace App\Entity;

	use App\Entity\Strings\CharmRankStrings;
	use App\Localization\TranslatableEntityInterface;
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
	class CharmRank implements EntityInterface, TranslatableEntityInterface {
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
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\CharmRankStrings",
		 *     mappedBy="charmRank",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
		 * )
		 *
		 * @var Collection|Selectable|CharmRankStrings[]
		 */
		private $strings;

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
		 * CharmRank constructor.
		 *
		 * @param Charm $charm
		 * @param int   $level
		 */
		public function __construct(Charm $charm, int $level) {
			$this->charm = $charm;
			$this->level = $level;
			$this->skills = new ArrayCollection();
			$this->strings = new ArrayCollection();
		}

		/**
		 * @return Charm
		 */
		public function getCharm(): Charm {
			return $this->charm;
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
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * {@inheritdoc}
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new CharmRankStrings($this, $language));

			return $strings;
		}

	}