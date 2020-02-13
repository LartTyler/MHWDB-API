<?php
	namespace App\Entity;

	use App\Entity\Strings\EndemicLifeStrings;
	use App\Game\EndemicLifeSpawnCondition;
	use App\Game\EndemicLifeType;
	use App\Localization\TranslatableEntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="endemic_life")
	 */
	class EndemicLife implements EntityInterface, TranslatableEntityInterface {
		use EntityTrait;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\EndemicLifeType", "values"})
		 *
		 * @ORM\Column(type="string", length=12)
		 *
		 * @var string
		 * @see EndemicLifeType
		 */
		private $type;

		/**
		 * @Assert\Range(min="1")
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $researchPointValue;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\EndemicLifeStrings",
		 *     mappedBy="endemicLife",
		 *     orphanRemoval=true,
		 *     cascade={"all"},
		 *     fetch="EAGER"
		 * )
		 *
		 * @var Collection|Selectable|EndemicLifeStrings[]
		 */
		private $strings;

		/**
		 * @Assert\Count(min="1")
		 *
		 * @ORM\ManyToMany(targetEntity="App\Entity\Location")
		 * @ORM\JoinTable(name="endemic_life_locations", inverseJoinColumns={@ORM\JoinColumn(unique=true)})
		 *
		 * @var Collection|Selectable|Location[]
		 */
		private $locations;

		/**
		 * @Assert\Choice(multiple=true, callback={"App\Game\EndemicLifeSpawnCondition", "values"})
		 *
		 * @ORM\Column(type="string", length=5)
		 *
		 * @var string[]
		 * @see EndemicLifeSpawnCondition
		 */
		private $spawnConditions = [];

		/**
		 * EndemicLife constructor.
		 *
		 * @param string $type
		 * @param int    $researchPointValue
		 */
		public function __construct(string $type, int $researchPointValue) {
			$this->type = $type;
			$this->researchPointValue = $researchPointValue;

			$this->strings = new ArrayCollection();
			$this->locations = new ArrayCollection();
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
		 * @return int
		 */
		public function getResearchPointValue(): int {
			return $this->researchPointValue;
		}

		/**
		 * @param int $researchPointValue
		 *
		 * @return $this
		 */
		public function setResearchPointValue(int $researchPointValue) {
			$this->researchPointValue = $researchPointValue;

			return $this;
		}

		/**
		 * @return string[]
		 */
		public function getSpawnConditions(): array {
			return $this->spawnConditions;
		}

		/**
		 * @param string[] $spawnConditions
		 *
		 * @return $this
		 */
		public function setSpawnConditions(array $spawnConditions) {
			$this->spawnConditions = $spawnConditions;

			return $this;
		}

		/**
		 * @return Collection|Selectable|EndemicLifeStrings[]
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return EndemicLifeStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->strings->add($strings = new EndemicLifeStrings($this, $language));

			return $strings;
		}

		/**
		 * @return Location[]|Collection|Selectable
		 */
		public function getLocations(): Collection {
			return $this->locations;
		}
	}