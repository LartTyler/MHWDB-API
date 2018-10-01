<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="ailments")
	 *
	 * Class Ailment
	 *
	 * @package App\Entity
	 */
	class Ailment implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\Column(type="string", length=32, unique=true)
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
		 * @ORM\OneToOne(
		 *     targetEntity="App\Entity\AilmentRecovery",
		 *     mappedBy="ailment",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
		 * )
		 *
		 * @var AilmentRecovery
		 */
		private $recovery;

		/**
		 * @ORM\OneToOne(
		 *     targetEntity="App\Entity\AilmentProtection",
		 *     mappedBy="ailment",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
		 * )
		 *
		 * @var AilmentProtection
		 */
		private $protection;

		/**
		 * Ailment constructor.
		 *
		 * @param string $name
		 * @param string $description
		 */
		public function __construct(string $name, string $description) {
			$this->name = $name;
			$this->description = $description;

			$this->recovery = new AilmentRecovery($this);
			$this->protection = new AilmentProtection($this);
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

		/**
		 * @return AilmentRecovery
		 */
		public function getRecovery(): AilmentRecovery {
			return $this->recovery;
		}

		/**
		 * @return AilmentProtection
		 */
		public function getProtection(): AilmentProtection {
			return $this->protection;
		}
	}