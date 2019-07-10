<?php
	namespace App\Entity;

	use App\Game\Element;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="weapon_elements")
	 *
	 * Class WeaponElement
	 *
	 * @package App\Entity
	 */
	class WeaponElement implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Weapon", inversedBy="elements")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Weapon
		 */
		private $weapon;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\Element", "getAllowedWeaponElements"})
		 *
		 * @ORM\Column(type="string", length=16)
		 *
		 * @var string
		 * @see Element
		 */
		private $type;

		/**
		 * @Assert\Range(min=1)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $damage;

		/**
		 * @ORM\Column(type="boolean")
		 *
		 * @var bool
		 */
		private $hidden;

		/**
		 * WeaponElement constructor.
		 *
		 * @param Weapon $weapon
		 * @param string $type
		 * @param int    $damage
		 * @param bool   $hidden
		 */
		public function __construct(Weapon $weapon, string $type, int $damage, bool $hidden = false) {
			$this->weapon = $weapon;
			$this->type = strtolower($type);
			$this->damage = $damage;
			$this->hidden = $hidden;
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
		 * @return int
		 */
		public function getDamage(): int {
			return $this->damage;
		}

		/**
		 * @param int $damage
		 *
		 * @return $this
		 */
		public function setDamage(int $damage) {
			$this->damage = $damage;

			return $this;
		}

		/**
		 * @return bool
		 */
		public function isHidden(): bool {
			return $this->hidden;
		}

		/**
		 * @param bool $hidden
		 *
		 * @return $this
		 */
		public function setHidden(bool $hidden) {
			$this->hidden = $hidden;

			return $this;
		}
	}