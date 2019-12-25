<?php

	namespace App\Entity\Strings;

	use App\Entity\Armor;
	use App\Localization\StringsEntityTrait;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="armor_strings",
	 *     uniqueConstraints={
	 *         @ORM\UniqueConstraint(columns={"armor_id", "language"})
	 *     }
	 * )
	 */
	class ArmorStrings implements EntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Armor", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Armor
		 */
		private $armor;

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
		 * ArmorStrings constructor.
		 *
		 * @param Armor  $armor
		 * @param string $language
		 */
		public function __construct(Armor $armor, string $language) {
			$this->armor = $armor;
			$this->language = $language;
		}

		/**
		 * @return Armor
		 */
		public function getArmor(): Armor {
			return $this->armor;
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