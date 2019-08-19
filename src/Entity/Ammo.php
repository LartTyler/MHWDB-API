<?php
	namespace App\Entity;

	use App\Game\AmmoType;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="weapon_ammo",
	 *     indexes={
	 *     		@ORM\Index(columns={"type"})
	 *     }
	 * )
	 */
	class Ammo implements EntityInterface, LengthCachingEntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Weapon", inversedBy="ammo")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Weapon
		 */
		protected $weapon;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\AmmoType", "all"})
		 *
		 * @ORM\Column(type="string", length=32)
		 *
		 * @var string
		 * @see AmmoType
		 */
		protected $type;

		/**
		 * @Assert\All({
		 * 		@Assert\NotNull(),
		 * 		@Assert\Type("integer"),
		 * 		@Assert\Range(min=0, max=99)
		 * })
		 *
		 * @ORM\Column(type="json")
		 *
		 * @var int[]
		 */
		protected $capacities = [];

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "capacities.length"
		 */
		protected $capacitiesLength = 0;

		/**
		 * AbstractAmmoCapacity constructor.
		 *
		 * @param Weapon $weapon
		 * @param string $type
		 * @see AmmoType
		 */
		public function __construct(Weapon $weapon, string $type) {
			$this->weapon = $weapon;
			$this->type = $type;
		}

		/**
		 * @return Weapon
		 */
		public function getWeapon(): Weapon {
			return $this->weapon;
		}

		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}

		/**
		 * @return int[]
		 */
		public function getCapacities(): array {
			return $this->capacities;
		}

		/**
		 * @param int[] $capacities
		 *
		 * @return $this
		 */
		public function setCapacities(array $capacities) {
			$this->capacities = $capacities;

			return $this;
		}

		/**
		 * @return bool
		 */
		public function isEmpty(): bool {
			return sizeof($this->capacities) === 0 || sizeof(array_filter($this->capacities)) === 0;
		}

		/**
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->capacitiesLength = sizeof($this->capacities);
		}
	}