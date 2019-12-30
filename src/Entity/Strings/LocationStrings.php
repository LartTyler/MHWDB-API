<?php
	namespace App\Entity\Strings;

	use App\Entity\Location;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="location_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"location_id", "language"})}
	 * )
	 */
	class LocationStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Location
		 */
		private $location;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Length(max="32")
		 *
		 * @ORM\Column(type="string", length=32, unique=true)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * LocationStrings constructor.
		 *
		 * @param Location $location
		 * @param string   $language
		 */
		public function __construct(Location $location, string $language) {
			$this->location = $location;
			$this->language = $language;
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
	}