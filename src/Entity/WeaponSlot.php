<?php
	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity(readOnly=true)
	 * @ORM\Table(name="weapon_slots")
	 *
	 * @package App\Entity
	 */
	class WeaponSlot extends Slot {
		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Weapon", inversedBy="slots")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Weapon
		 */
		protected $weapon;

		/**
		 * WeaponSlot constructor.
		 *
		 * @param Weapon $weapon
		 * @param int    $rank
		 */
		public function __construct(Weapon $weapon, int $rank) {
			parent::__construct($rank);

			$this->weapon = $weapon;
		}

		/**
		 * @return mixed
		 */
		public function getWeapon() {
			return $this->weapon;
		}
	}