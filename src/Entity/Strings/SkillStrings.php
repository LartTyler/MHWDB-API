<?php
	namespace App\Entity\Strings;

	use App\Entity\Skill;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="skill_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"skill_id", "language"})}
	 * )
	 */
	class SkillStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Skill", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Skill
		 */
		private $skill;

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
		 * @ORM\Column(type="text")
		 *
		 * @var string
		 */
		private $description;

		/**
		 * SkillStrings constructor.
		 *
		 * @param Skill  $skill
		 * @param string $language
		 */
		public function __construct(Skill $skill, string $language) {
			$this->skill = $skill;
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