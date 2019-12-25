<?php

	namespace App\Entity\Strings;

	use App\Entity\Charm;
	use App\Localization\StringsEntityTrait;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="charm_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"charm_id", "language"})}
	 * )
	 */
	class CharmStrings implements EntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Charm", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Charm
		 */
		private $charm;

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
		 * CharmStrings constructor.
		 *
		 * @param Charm  $charm
		 * @param string $language
		 */
		public function __construct(Charm $charm, string $language) {
			$this->charm = $charm;
			$this->language = $language;
		}

		/**
		 * @return Charm
		 */
		public function getCharm(): Charm {
			return $this->charm;
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