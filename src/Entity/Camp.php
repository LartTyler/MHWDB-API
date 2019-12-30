<?php
	namespace App\Entity;

	use App\Entity\Strings\CampStrings;
	use App\Localization\TranslatableEntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="camps")
	 *
	 * Class Camp
	 *
	 * @package App\Entity
	 */
	class Camp implements EntityInterface, TranslatableEntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="camps")
		 *
		 * @var Location
		 */
		private $location;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Range(min=1)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $zone;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\CampStrings",
		 *     mappedBy="camp",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
		 * )
		 *
		 * @var Collection|Selectable|CampStrings[]
		 */
		private $strings;

		/**
		 * Camp constructor.
		 *
		 * @param Location $location
		 * @param int      $zone
		 */
		public function __construct(Location $location, int $zone) {
			$this->location = $location;
			$this->zone = $zone;

			$this->strings = new ArrayCollection();
		}

		/**
		 * @return Location
		 */
		public function getLocation(): Location {
			return $this->location;
		}

		/**
		 * @return int
		 */
		public function getZone(): int {
			return $this->zone;
		}

		/**
		 * @return CampStrings[]|Collection|Selectable
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return CampStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new CampStrings($this, $language));

			return $strings;
		}
	}