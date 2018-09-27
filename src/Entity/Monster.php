<?php
	namespace App\Entity;

	use App\Game\Element;
	use App\Game\MonsterSpecies;
	use App\Game\MonsterType;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity(repositoryClass="App\Repository\MonsterRepository")
	 * @ORM\Table(name="monsters")
	 *
	 * Class Monster
	 *
	 * @package App\Entity
	 */
	class Monster implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\Column(type="string", length=64, unique=true)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * @ORM\Column(type="string", length=32)
		 *
		 * @var string
		 * @see MonsterType
		 */
		private $type;

		/**
		 * @ORM\Column(type="string", length=32)
		 *
		 * @var string
		 * @see MonsterSpecies
		 */
		private $species;

		/**
		 * @ORM\ManyToMany(targetEntity="App\Entity\Ailment")
		 * @ORM\JoinTable(name="monster_ailments")
		 *
		 * @var Ailment[]|Collection|Selectable
		 */
		private $ailments;

		/**
		 * @ORM\ManyToMany(targetEntity="App\Entity\Location")
		 * @ORM\JoinTable(name="monster_locations")
		 *
		 * @var Location[]|Collection|Selectable
		 */
		private $locations;

		/**
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\MonsterResistance",
		 *     mappedBy="monster",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
		 *	 )
		 *
		 * @var MonsterResistance[]|Collection|Selectable
		 */
		private $resistances;

		/**
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\MonsterWeakness",
		 *     mappedBy="monster",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
		 * )
		 *
		 * @var MonsterWeakness[]|Collection|Selectable
		 */
		private $weaknesses;

		/**
		 * @ORM\Column(type="text", nullable=true)
		 *
		 * @var string|null
		 */
		private $description = null;

		/**
		 * @ORM\Column(type="json")
		 *
		 * @var string[]
		 * @see Element::DAMAGE
		 */
		private $elements = [];

		/**
		 * Monster constructor.
		 *
		 * @param string $name
		 * @param string $type
		 * @param string $species
		 */
		public function __construct(string $name, string $type, string $species) {
			$this->name = $name;
			$this->type = $type;
			$this->species = $species;

			$this->ailments = new ArrayCollection();
			$this->locations = new ArrayCollection();
			$this->resistances = new ArrayCollection();
			$this->weaknesses = new ArrayCollection();
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
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
		public function getSpecies(): string {
			return $this->species;
		}

		/**
		 * @param string $species
		 *
		 * @return $this
		 */
		public function setSpecies(string $species) {
			$this->species = $species;

			return $this;
		}

		/**
		 * @return null|string
		 */
		public function getDescription(): ?string {
			return $this->description;
		}

		/**
		 * @param null|string $description
		 *
		 * @return $this
		 */
		public function setDescription(?string $description) {
			$this->description = $description;

			return $this;
		}

		/**
		 * @return string[]
		 */
		public function getElements(): array {
			return $this->elements;
		}

		/**
		 * @param string[] $elements
		 *
		 * @return $this
		 */
		public function setElements(array $elements) {
			$this->elements = $elements;

			return $this;
		}

		/**
		 * @return Ailment[]|Collection|Selectable
		 */
		public function getAilments() {
			return $this->ailments;
		}

		/**
		 * @return Location[]|Collection|Selectable
		 */
		public function getLocations() {
			return $this->locations;
		}

		/**
		 * @return MonsterResistance[]|Collection|Selectable
		 */
		public function getResistances() {
			return $this->resistances;
		}

		/**
		 * @return MonsterWeakness[]|Collection|Selectable
		 */
		public function getWeaknesses() {
			return $this->weaknesses;
		}
	}