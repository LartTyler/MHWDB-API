<?php

	namespace App\Entity\Strings;

	use App\Entity\Camp;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="camp_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"camp_id", "language"})}
	 * )
	 */
	class CampStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Camp", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Camp
		 */
		private $camp;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Length(max="64")
		 *
		 * @ORM\Column(type="string", length=64)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * CampStrings constructor.
		 *
		 * @param Camp   $camp
		 * @param string $language
		 */
		public function __construct(Camp $camp, string $language) {
			$this->camp = $camp;
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