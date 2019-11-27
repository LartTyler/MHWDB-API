<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait as BaseEntityTrait;
	use Doctrine\ORM\Mapping as ORM;

	trait EntityTrait {
		use BaseEntityTrait {
			__toString as __baseToString;
		}

		/**
		 * @ORM\Id()
		 * @ORM\GeneratedValue()
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 *
		 * @var int|null
		 */
		protected $id;

		/**
		 * Explicitly sets the entity's ID. Used during create-only imports to ensure that any missing objects are
		 * assigned a stable ID, instead of the next ID in the auto-generated sequence.
		 *
		 * @param int $id
		 *
		 * @return $this
		 * @throws \LogicException if the entity's ID is already set
		 */
		public function setId(int $id) {
			if ($this->getId())
				throw new \LogicException('Cannot update an entity\'s ID once it has been set');

			$this->id = $id;

			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function __toString(): string {
			return $this->__baseToString();
		}
	}