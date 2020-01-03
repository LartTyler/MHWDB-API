<?php
	namespace App\Entity\Strings;

	use App\Entity\Weapon;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="weapon_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"weapon_id", "language"})}
	 * )
	 */
	class WeaponStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Weapon", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Weapon
		 */
		private $weapon;

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
		 * WeaponStrings constructor.
		 *
		 * @param Weapon $weapon
		 * @param string $language
		 */
		public function __construct(Weapon $weapon, string $language) {
			$this->weapon = $weapon;
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