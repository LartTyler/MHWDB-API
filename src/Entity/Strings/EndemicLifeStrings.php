<?php
	namespace App\Entity\Strings;

	use App\Entity\EndemicLife;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="endemic_life_strings")
	 */
	class EndemicLifeStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\EndemicLife", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var EndemicLife
		 */
		private $endemicLife;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Length(max="64")
		 *
		 * @ORM\Column(type="string", length=64)
		 *
		 * @var string|null
		 */
		private $name = null;

		/**
		 * @ORM\Column(type="text", nullable=true)
		 *
		 * @var string
		 */
		private $description = null;

		/**
		 * EndemicLifeStrings constructor.
		 *
		 * @param EndemicLife $endemicLife
		 * @param string      $language
		 */
		public function __construct(EndemicLife $endemicLife, string $language) {
			$this->endemicLife = $endemicLife;
			$this->language = $language;
		}

		/**
		 * @return string|null
		 */
		public function getName(): ?string {
			return $this->name;
		}

		/**
		 * @param string|null $name
		 *
		 * @return $this
		 */
		public function setName(?string $name) {
			$this->name = $name;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getDescription(): string {
			return $this->description;
		}

		/**
		 * @param string $description
		 *
		 * @return $this
		 */
		public function setDescription(string $description) {
			$this->description = $description;

			return $this;
		}
	}