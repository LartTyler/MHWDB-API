<?php
	namespace App\Entity\Strings;

	use App\Entity\Decoration;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="decoration_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"decoration_id", "language"})}
	 * )
	 */
	class DecorationStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Decoration", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Decoration
		 */
		private $decoration;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Length(max="64")
		 *
		 * @ORM\Column(type="string", length=64, unique=true)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * DecorationStrings constructor.
		 *
		 * @param Decoration $decoration
		 * @param string     $language
		 */
		public function __construct(Decoration $decoration, string $language) {
			$this->decoration = $decoration;
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