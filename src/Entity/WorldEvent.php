<?php
	namespace App\Entity;

	use App\Game\WorldEventType;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="world_events",
	 *     uniqueConstraints={
	 *     		@ORM\UniqueConstraint(columns={"name", "startTimestamp"})
	 *     }
	 * )
	 */
	class WorldEvent implements EntityInterface {
		use EntityTrait;

		/**
		 * @Assert\NotBlank()
		 *
		 * @ORM\Column(type="string", length=128)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\WorldEventType", "all"})
		 *
		 * @ORM\Column(type="string", length=128)
		 *
		 * @var string
		 * @see WorldEventType
		 */
		private $type;

		/**
		 * @ORM\Column(type="datetime_immutable")
		 *
		 * @var \DateTimeImmutable
		 */
		private $startTimestamp;

		/**
		 * @ORM\Column(type="datetime_immutable")
		 *
		 * @var \DateTimeImmutable
		 */
		private $endTimestamp;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Location")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Location
		 */
		private $location;

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
		 * WorldEvent constructor.
		 *
		 * @param string             $name
		 * @param string             $type
		 * @param \DateTimeImmutable $startTimestamp
		 * @param \DateTimeImmutable $endTimestamp
		 * @param Location           $location
		 */
		public function __construct(
			string $name,
			string $type,
			\DateTimeImmutable $startTimestamp,
			\DateTimeImmutable $endTimestamp,
			Location $location
		) {
			$this->name = $name;
			$this->type = $type;
			$this->startTimestamp = $startTimestamp;
			$this->endTimestamp = $endTimestamp;
			$this->location = $location;
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}

		/**
		 * @return \DateTimeImmutable
		 */
		public function getStartTimestamp(): \DateTimeImmutable {
			return $this->startTimestamp;
		}

		/**
		 * @return \DateTimeImmutable
		 */
		public function getEndTimestamp(): \DateTimeImmutable {
			return $this->endTimestamp;
		}

		/**
		 * @return Location
		 */
		public function getLocation(): Location {
			return $this->location;
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