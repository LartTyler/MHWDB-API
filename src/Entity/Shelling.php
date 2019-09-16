<?php
	namespace App\Entity;

	use App\Game\ShellingType;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity(readOnly=true)
	 * @ORM\Table(name="weapon_shelling")
	 */
	class Shelling implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\OneToOne(targetEntity="App\Entity\Weapon", inversedBy="shelling")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Weapon
		 */
		private $weapon;

		/**
		 * @Assert\NotNull()
		 * @Assert\Choice(callback={"App\Game\ShellingType", "all"})
		 *
		 * @ORM\Column(type="string", length=16)
		 *
		 * @var string
		 * @see ShellingType
		 */
		private $type;

		/**
		 * @Assert\NotNull()
		 * @Assert\Range(min=1)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $level;

		/**
		 * Shelling constructor.
		 *
		 * @param Weapon $weapon
		 * @param string $type
		 * @param int    $level
		 */
		public function __construct(Weapon $weapon, string $type, int $level) {
			$this->weapon = $weapon;
			$this->type = $type;
			$this->level = $level;
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
	}