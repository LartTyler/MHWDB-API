<?php
	namespace App\Entity\Strings;

	use App\Entity\WorldEvent;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="world_event_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"world_event_id", "language"})}
	 * )
	 */
	class WorldEventStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\WorldEvent", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var WorldEvent
		 */
		private $event;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Length(max="128")
		 *
		 * @ORM\Column(type="string", length=128)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * @ORM\Column(type="text", nullable=true)
		 *
		 * @var string|null
		 */
		private $description = null;

		/**
		 * @ORM\Column(type="text", nullable=true)
		 *
		 * @var string|null
		 */
		private $requirements = null;

		/**
		 * @ORM\Column(type="text", nullable=true)
		 *
		 * @var string|null
		 */
		private $successConditions = null;

		/**
		 * WorldEventStrings constructor.
		 *
		 * @param WorldEvent $event
		 * @param string     $language
		 */
		public function __construct(WorldEvent $event, string $language) {
			$this->event = $event;
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

		/**
		 * @return string|null
		 */
		public function getRequirements(): ?string {
			return $this->requirements;
		}

		/**
		 * @param string|null $requirements
		 *
		 * @return $this
		 */
		public function setRequirements(?string $requirements) {
			$this->requirements = $requirements;

			return $this;
		}

		/**
		 * @return string|null
		 */
		public function getSuccessConditions(): ?string {
			return $this->successConditions;
		}

		/**
		 * @param string|null $successConditions
		 *
		 * @return $this
		 */
		public function setSuccessConditions(?string $successConditions) {
			$this->successConditions = $successConditions;

			return $this;
		}
	}