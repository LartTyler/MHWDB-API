<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
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
	class Camp implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="camps")
		 *
		 * @var Location
		 */
		private $location;

		/**
		 * @Assert\NotBlank()
		 *
		 * @ORM\Column(type="string")
		 *
		 * @var string
		 */
		private $name;

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
		 * Camp constructor.
		 *
		 * @param Location $location
		 * @param string   $name
		 * @param int      $zone
		 */
		public function __construct(Location $location, string $name, int $zone) {
			$this->location = $location;
			$this->name = $name;
			$this->zone = $zone;
		}

		/**
		 * @return Location
		 */
		public function getLocation(): Location {
			return $this->location;
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
		 * @return int
		 */
		public function getZone(): int {
			return $this->zone;
		}
	}