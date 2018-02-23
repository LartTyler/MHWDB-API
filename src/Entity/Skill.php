<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class Skill implements EntityInterface {
		use EntityTrait;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var string[]
		 */
		private $ranks;

		/**
		 * Skill constructor.
		 *
		 * @param string   $name
		 * @param string[] $ranks
		 */
		public function __construct($name, array $ranks) {
			$this->name = $name;
			$this->ranks = $ranks;
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
		 * @return string[]
		 */
		public function getRanks(): array {
			return $this->ranks;
		}

		/**
		 * @param string[] $ranks
		 *
		 * @return $this
		 */
		public function setRanks(array $ranks) {
			$this->ranks = $ranks;

			return $this;
		}
	}