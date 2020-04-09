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
	 * @ORM\Entity(repositoryClass="App\Repository\WorldEventRepository")
	 * @ORM\Table(name="world_events")
	 */
	class WorldEvent implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Quest", inversedBy="events")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Quest
		 */
		private $quest;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\WorldEventType", "values"})
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
		 * @Assert\Choice(callback={"App\Game\PlatformExclusivityType", "values"})
		 *
		 * @ORM\Column(type="string", length=16, nullable=true)
		 *
		 * @var string|null
		 * @see PlatformExclusivityType
		 */
		private $exclusive = null;

		/**
		 * WorldEvent constructor.
		 *
		 * @param Quest              $quest
		 * @param string             $type
		 * @param string             $expansion
		 * @param string             $platform
		 * @param \DateTimeImmutable $startTimestamp
		 * @param \DateTimeImmutable $endTimestamp
		 */
		public function __construct(
			Quest $quest,
			string $type,
			string $expansion,
			string $platform,
			\DateTimeImmutable $startTimestamp,
			\DateTimeImmutable $endTimestamp
		) {
			$this->quest = $quest;
			$this->type = $type;
			$this->expansion = $expansion;
			$this->platform = $platform;
			$this->startTimestamp = $startTimestamp;
			$this->endTimestamp = $endTimestamp;
		}

		/**
		 * @return Quest
		 */
		public function getQuest(): Quest {
			return $this->quest;
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
	}