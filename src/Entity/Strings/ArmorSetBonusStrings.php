<?php

	namespace App\Entity\Strings;

	use App\Entity\ArmorSetBonus;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="armor_set_bonus_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"armor_set_bonus_id", "language"})}
	 * )
	 */
	class ArmorSetBonusStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\ArmorSetBonus", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var ArmorSetBonus
		 */
		private $armorSetBonus;

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
		 * ArmorSetBonusStrings constructor.
		 *
		 * @param ArmorSetBonus $armorSetBonus
		 * @param string        $language
		 */
		public function __construct(ArmorSetBonus $armorSetBonus, string $language) {
			$this->armorSetBonus = $armorSetBonus;
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