<?php
	namespace App\Entity;

	use App\Game\DamageType;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="motion_values",
	 *     uniqueConstraints={
	 *         @ORM\UniqueConstraint(columns={"weapon_type", "name"})
	 *     }
	 * )
	 *
	 * Class MotionValue
	 *
	 * @package App\Entity
	 */
	class MotionValue implements EntityInterface, LengthCachingEntityInterface {
		use EntityTrait;

		/**
		 * @ORM\Column(type="string", length=64)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * @ORM\Column(type="string", length=32)
		 *
		 * @var string
		 */
		private $weaponType;

		/**
		 * @ORM\Column(type="string", length=32, nullable=true)
		 *
		 * @var string
		 * @see DamageType
		 */
		private $damageType = null;

		/**
		 * @ORM\Column(type="smallint", options={"unsigned": true}, name="stun_potency", nullable=true)
		 *
		 * @var int|null
		 */
		private $stun = null;

		/**
		 * @ORM\Column(type="smallint", options={"unsigned": true}, name="exhaust_potency", nullable=true)
		 *
		 * @var int|null
		 */
		private $exhaust = null;

		/**
		 * @ORM\Column(type="json")
		 *
		 * @var int[]
		 */
		private $hits = [];

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "hits.length"
		 */
		private $hitsLength = 0;

		/**
		 * MotionValue constructor.
		 *
		 * @param string $name
		 * @param string $weaponType
		 */
		public function __construct(string $name, string $weaponType) {
			$this->name = $name;
			$this->weaponType = $weaponType;
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
		 * @return string
		 */
		public function getWeaponType(): string {
			return $this->weaponType;
		}

		/**
		 * @param string|null $weaponType
		 *
		 * @return $this
		 */
		public function setWeaponType(?string $weaponType) {
			$this->weaponType = $weaponType;

			return $this;
		}

		/**
		 * @return string|null
		 */
		public function getDamageType(): ?string {
			return $this->damageType;
		}

		/**
		 * @param string|null $damageType
		 *
		 * @return $this
		 */
		public function setDamageType(?string $damageType) {
			$this->damageType = $damageType;

			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getStun(): ?int {
			return $this->stun;
		}

		/**
		 * @param int|null $stun
		 *
		 * @return $this
		 */
		public function setStun(?int $stun) {
			$this->stun = $stun;

			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getExhaust(): ?int {
			return $this->exhaust;
		}

		/**
		 * @param int|null $exhaust
		 *
		 * @return $this
		 */
		public function setExhaust(?int $exhaust) {
			$this->exhaust = $exhaust;

			return $this;
		}

		/**
		 * @return int[]
		 */
		public function getHits(): array {
			return $this->hits;
		}

		/**
		 * @param int[] $hits
		 *
		 * @return $this
		 */
		public function setHits(array $hits) {
			$this->hits = $hits;

			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->hitsLength = sizeof($this->hits);
		}
	}