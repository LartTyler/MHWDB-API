<?php
	namespace App\Entity\Strings;

	use App\Entity\MotionValue;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="motion_value_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"motion_value_id", "language"})}
	 * )
	 */
	class MotionValueStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\MotionValue", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var MotionValue
		 */
		private $motionValue;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Length(max="64")
		 *
		 * @ORM\Column(type="string", length=64)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * MotionValueStrings constructor.
		 *
		 * @param MotionValue $motionValue
		 * @param string      $language
		 */
		public function __construct(MotionValue $motionValue, string $language) {
			$this->motionValue = $motionValue;
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