<?php
	namespace App\Entity;

	use App\Game\Expansion;
	use App\Game\PlatformExclusivityType;
	use App\Game\PlatformType;
	use App\Game\WorldEventType;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="world_events",
	 *     uniqueConstraints={
	 *     		@ORM\UniqueConstraint(columns={"platform", "name", "expansion", "start_timestamp"})
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
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\Expansion", "values"})
		 *
		 * @ORM\Column(type="string", length=32)
		 *
		 * @var string
		 * @see Expansion
		 */
		private $expansion;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\PlatformType", "all"})
		 *
		 * @ORM\Column(type="string", length=16)
		 *
		 * @var string
		 * @see PlatformType
		 */
		private $platform;

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
		 * @Assert\NotBlank()
		 * @Assert\GreaterThanOrEqual(1)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $questRank;

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
		 * @Assert\Choice(callback={"App\Game\PlatformExclusivityType", "all"})
		 *
		 * @ORM\Column(type="string", length=16, nullable=true)
		 *
		 * @var string|null
		 * @see PlatformExclusivityType
		 */
		private $exclusive = null;

		/**
		 * @ORM\Column(type="boolean")
		 *
		 * @var bool
		 */
		private $masterRank = false;

		/**
		 * WorldEvent constructor.
		 *
		 * @param string             $name
		 * @param string             $type
		 * @param string             $expansion
		 * @param string             $platform
		 * @param \DateTimeImmutable $startTimestamp
		 * @param \DateTimeImmutable $endTimestamp
		 * @param Location           $location
		 * @param int                $questRank
		 */
		public function __construct(
			string $name,
			string $type,
			string $expansion,
			string $platform,
			\DateTimeImmutable $startTimestamp,
			\DateTimeImmutable $endTimestamp,
			Location $location,
			int $questRank
		) {
			$this->name = $name;
			$this->type = $type;
			$this->expansion = $expansion;
			$this->platform = $platform;
			$this->startTimestamp = $startTimestamp;
			$this->endTimestamp = $endTimestamp;
			$this->location = $location;
			$this->questRank = $questRank;
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
		 * @return string
		 * @see Expansion
		 */
		public function getExpansion(): string {
			return $this->expansion;
		}

		/**
		 * @return string
		 */
		public function getPlatform(): string {
			return $this->platform;
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
		 * @return int
		 */
		public function getQuestRank(): int {
			return $this->questRank;
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

		/**
		 * @return string|null
		 */
		public function getExclusive(): ?string {
			return $this->exclusive;
		}

		/**
		 * @param string|null $exclusive
		 *
		 * @return $this
		 */
		public function setExclusive(?string $exclusive) {
			$this->exclusive = $exclusive;

			return $this;
		}

		/**
		 * @return bool
		 */
		public function isMasterRank(): bool {
			return $this->masterRank;
		}

		/**
		 * @param bool $masterRank
		 *
		 * @return $this
		 */
		public function setMasterRank(bool $masterRank) {
			$this->masterRank = $masterRank;

			return $this;
		}
	}