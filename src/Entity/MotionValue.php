<?php
	namespace App\Entity;

	use App\Entity\Strings\MotionValueStrings;
	use App\Game\DamageType;
	use App\Game\WeaponType;
	use App\Localization\TranslatableEntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="motion_values")
	 */
	class MotionValue implements EntityInterface, TranslatableEntityInterface, LengthCachingEntityInterface {
		use EntityTrait;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\WeaponType", "all"})
		 *
		 * @ORM\Column(type="string", length=32)
		 *
		 * @var string
		 * @see WeaponType
		 */
		private $weaponType;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\MotionValueStrings",
		 *     mappedBy="motionValue",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
		 * )
		 *
		 * @var Collection|Selectable|MotionValueStrings[]
		 */
		private $strings;

		/**
		 * @Assert\Choice(callback={"App\Game\DamageType", "all"})
		 *
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
		 * @Assert\All(constraints={
		 *     @Assert\Range(min=1)
		 * })
		 *
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
		 * @param string $weaponType
		 */
		public function __construct(string $weaponType) {
			$this->weaponType = $weaponType;

			$this->strings = new ArrayCollection();
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

		/**
		 * @return Collection|Selectable|MotionValueStrings[]
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return MotionValueStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new MotionValueStrings($this, $language));

			return $strings;
		}
	}