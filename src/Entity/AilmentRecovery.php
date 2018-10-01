<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity(repositoryClass="App\Repository\AilmentRecoveryRepository")
	 * @ORM\Table(name="ailment_recovery_methods")
	 *
	 * Class AilmentRecovery
	 *
	 * @package App\Entity
	 */
	class AilmentRecovery implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\OneToOne(targetEntity="App\Entity\Ailment", inversedBy="recovery")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Ailment
		 */
		private $ailment;

		/**
		 * @ORM\ManyToMany(targetEntity="App\Entity\Item")
		 * @ORM\JoinTable(name="ailment_recovery_items")
		 *
		 * @var Item[]|Collection|Selectable
		 */
		private $items;

		/**
		 * @ORM\Column(type="json")
		 *
		 * @var string[]
		 */
		private $actions = [];

		/**
		 * AilmentRecovery constructor.
		 *
		 * @param Ailment $ailment
		 */
		public function __construct(Ailment $ailment) {
			$this->ailment = $ailment;

			$this->items = new ArrayCollection();
		}

		/**
		 * @return Ailment
		 */
		public function getAilment(): Ailment {
			return $this->ailment;
		}

		/**
		 * @return string[]
		 */
		public function getActions(): array {
			return $this->actions;
		}

		/**
		 * @param string[] $actions
		 *
		 * @return $this
		 */
		public function setActions(array $actions) {
			$this->actions = $actions;

			return $this;
		}

		/**
		 * @return Item[]|Collection|Selectable
		 */
		public function getItems() {
			return $this->items;
		}
	}