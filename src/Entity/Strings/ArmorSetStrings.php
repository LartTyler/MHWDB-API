<?php

	namespace App\Entity\Strings;

	use App\Entity\ArmorSet;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="armor_set_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"armor_set_id", "language"})}
	 * )
	 */
	class ArmorSetStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\ArmorSet", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var ArmorSet
		 */
		private $armorSet;

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
		 * ArmorSetStrings constructor.
		 *
		 * @param ArmorSet $armorSet
		 * @param string   $language
		 */
		public function __construct(ArmorSet $armorSet, string $language) {
			$this->armorSet = $armorSet;
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