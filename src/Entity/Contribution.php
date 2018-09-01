<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="contributions")
	 *
	 * Class Contribution
	 *
	 * @package App\Entity
	 */
	class Contribution implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\User")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var User
		 */
		private $submittedBy;

		/**
		 * @ORM\Column(type="string", length=64)
		 *
		 * @var string
		 */
		private $entity;

		/**
		 * @ORM\Column(type="datetime_immutable")
		 *
		 * @var \DateTimeImmutable
		 */
		private $submittedDate;

		/**
		 * Contribution constructor.
		 *
		 * @param User   $submittedBy
		 * @param string $entity
		 *
		 * @throws \Exception
		 */
		public function __construct(User $submittedBy, string $entity) {
			$this->submittedBy = $submittedBy;
			$this->entity = $entity;
			$this->submittedDate = new \DateTimeImmutable();
		}

		/**
		 * @return User
		 */
		public function getSubmittedBy(): User {
			return $this->submittedBy;
		}

		/**
		 * @return string
		 */
		public function getEntity(): string {
			return $this->entity;
		}

		/**
		 * @return \DateTimeImmutable
		 */
		public function getSubmittedDate(): \DateTimeImmutable {
			return $this->submittedDate;
		}
	}