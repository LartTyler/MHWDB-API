<?php
	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity(readOnly=true)
	 * @ORM\Table(name="armor_slots")
	 *
	 * @package App\Entity
	 */
	class ArmorSlot extends Slot {
		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Armor", inversedBy="slots")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Armor
		 */
		protected $armor;

		/**
		 * ArmorSlot constructor.
		 *
		 * @param Armor $armor
		 * @param int   $rank
		 */
		public function __construct(Armor $armor, int $rank) {
			parent::__construct($rank);

			$this->armor = $armor;
		}

		/**
		 * @return Armor
		 */
		public function getArmor(): Armor {
			return $this->armor;
		}
	}