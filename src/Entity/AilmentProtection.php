<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity(repositoryClass="App\Repository\AilmentProtectionRepository")
	 * @ORM\Table(name="ailment_protection_methods")
	 *
	 * Class AilmentProtection
	 *
	 * @package App\Entity
	 */
	class AilmentProtection implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\OneToOne(targetEntity="App\Entity\Ailment", inversedBy="protection")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Ailment
		 */
		private $ailment;

		/**
		 * @ORM\ManyToMany(targetEntity="App\Entity\Skill")
		 * @ORM\JoinTable(name="ailment_protection_skills")
		 *
		 * @var Skill[]|Collection|Selectable
		 */
		private $skills;

		/**
		 * @ORM\ManyToMany(targetEntity="App\Entity\Item")
		 * @ORM\JoinTable(name="ailment_protection_items")
		 *
		 * @var Item[]|Collection|Selectable
		 */
		private $items;

		/**
		 * AilmentProtection constructor.
		 *
		 * @param Ailment $ailment
		 */
		public function __construct(Ailment $ailment) {
			$this->ailment = $ailment;

			$this->skills = new ArrayCollection();
			$this->items = new ArrayCollection();
		}

		/**
		 * @return Ailment
		 */
		public function getAilment(): Ailment {
			return $this->ailment;
		}

		/**
		 * @return Skill[]|Collection|Selectable
		 */
		public function getSkills() {
			return $this->skills;
		}

		/**
		 * @return Item[]|Collection|Selectable
		 */
		public function getItems() {
			return $this->items;
		}
	}