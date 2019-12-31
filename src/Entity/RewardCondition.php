<?php
	namespace App\Entity;

	use App\Entity\Strings\RewardConditionStrings;
	use App\Game\Rank;
	use App\Game\RewardConditionType;
	use App\Localization\TranslatableEntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity(readOnly=true)
	 * @ORM\Table(name="reward_conditions")
	 *
	 * @package App\Entity
	 */
	class RewardCondition implements EntityInterface, TranslatableEntityInterface {
		use EntityTrait;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\RewardConditionType", "all"})
		 *
		 * @ORM\Column(type="string", length=32)
		 *
		 * @var string
		 * @see RewardConditionType
		 */
		private $type;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\Rank", "all"})
		 *
		 * @ORM\Column(type="string", length=16)
		 *
		 * @var string
		 * @see Rank
		 */
		private $rank;

		/**
		 * @Assert\NotBlank()
		 * @Assert\GreaterThanOrEqual(1)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $quantity;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Range(min=1, max=100)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $chance;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\RewardConditionStrings",
		 *     mappedBy="rewardCondition",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
		 * )
		 *
		 * @var Collection|Selectable|RewardConditionStrings[]
		 */
		private $strings;

		/**
		 * RewardCondition constructor.
		 *
		 * @param string $type
		 * @param string $rank
		 * @param int    $quantity
		 * @param int    $chance
		 */
		public function __construct(string $type, string $rank, int $quantity, int $chance) {
			$this->type = $type;
			$this->rank = $rank;
			$this->quantity = $quantity;
			$this->chance = $chance;

			$this->strings = new ArrayCollection();
		}

		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}

		/**
		 * @param string $type
		 *
		 * @return $this
		 */
		public function setType(string $type) {
			$this->type = $type;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getRank(): string {
			return $this->rank;
		}

		/**
		 * @param string $rank
		 *
		 * @return $this
		 */
		public function setRank(string $rank) {
			$this->rank = $rank;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getQuantity(): int {
			return $this->quantity;
		}

		/**
		 * @param int $quantity
		 *
		 * @return $this
		 */
		public function setQuantity(int $quantity) {
			$this->quantity = $quantity;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getChance(): int {
			return $this->chance;
		}

		/**
		 * @param int $chance
		 *
		 * @return $this
		 */
		public function setChance(int $chance) {
			$this->chance = $chance;

			return $this;
		}

		/**
		 * @return Collection|Selectable|RewardConditionStrings[]
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return RewardConditionStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new RewardConditionStrings($this, $language));

			return $strings;
		}
	}