<?php
	namespace App\Entity\Strings;

	use App\Entity\Ailment;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="ailment_strings",
	 *     uniqueConstraints={
	 *         @ORM\UniqueConstraint(columns={"ailment_id", "language"})
	 *     }
	 * )
	 */
	class AilmentStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Ailment", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Ailment
		 */
		private $ailment;

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
		 * @ORM\Column(type="text", nullable=true)
		 *
		 * @var string|null
		 */
		private $description;

		/**
		 * AilmentStrings constructor.
		 *
		 * @param Ailment $ailment
		 * @param string  $language
		 */
		public function __construct(Ailment $ailment, string $language) {
			$this->ailment = $ailment;
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

		/**
		 * @return string|null
		 */
		public function getDescription(): ?string {
			return $this->description;
		}

		/**
		 * @param string|null $description
		 *
		 * @return $this
		 */
		public function setDescription(?string $description) {
			$this->description = $description;

			return $this;
		}
	}