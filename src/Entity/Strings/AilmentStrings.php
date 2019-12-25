<?php
	namespace App\Entity\Strings;

	use App\Entity\Ailment;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="ailment_strings",
	 *     uniqueConstraints={
	 *         @ORM\UniqueConstraint(columns={"ailment_id", "language"})
	 *     }
	 * )
	 */
	class AilmentStrings implements EntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Ailment", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Ailment
		 */
		private $ailment;

		/**
		 * @ORM\Column(type="string", length=32, nullable=true, unique=true)
		 *
		 * @var string
		 */
		private $name = null;

		/**
		 * @ORM\Column(type="text")
		 *
		 * @var string
		 */
		private $description = null;

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
		 * @return Ailment
		 */
		public function getAilment(): Ailment {
			return $this->ailment;
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