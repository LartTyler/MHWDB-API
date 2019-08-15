<?php
	namespace App\Entity;

	use App\Game\PhialType;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity(readOnly=true)
	 * @ORM\Table(name="weapon_phials")
	 */
	class Phial implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\OneToOne(targetEntity="App\Entity\Weapon", mappedBy="phial")
		 *
		 * @var Weapon
		 */
		private $weapon;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\PhialType", "all"})
		 *
		 * @ORM\Column(type="string", length=32)
		 *
		 * @var string
		 * @see PhialType
		 */
		private $type;

		/**
		 * @Assert\Range(min=0)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true}, nullable=true)
		 *
		 * @var int|null
		 */
		private $damage = null;

		/**
		 * Phial constructor.
		 *
		 * @param Weapon $weapon
		 * @param string $type
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
		 * @see PhialType
		 */
		public function getType(): string {
			return $this->type;
		}

		/**
		 * @return int|null
		 */
		public function getDamage(): ?int {
			return $this->damage;
		}

		/**
		 * @param int|null $damage
		 *
		 * @return $this
		 */
		public function setDamage(?int $damage) {
			$this->damage = $damage;

			return $this;
		}
	}