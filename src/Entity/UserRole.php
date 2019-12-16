<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Security\Core\Role\Role;

	/**
	 * @ORM\Entity(readOnly=true)
	 * @ORM\Table(
	 *     name="user_roles",
	 *     uniqueConstraints={
	 *         @ORM\UniqueConstraint(columns={"user_id", "name"})
	 *     }
	 * )
	 *
	 * Class UserRole
	 *
	 * @package App\Entity
	 */
	class UserRole implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="roles")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var User
		 */
		private $user;

		/**
		 * @ORM\Column(type="string", length=32)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * UserRole constructor.
		 *
		 * @param User   $user
		 * @param string $name
		 */
		public function __construct(User $user, string $name) {
			$this->user = $user;
			$this->name = $name;
		}

		/**
		 * @return User
		 */
		public function getUser(): User {
			return $this->user;
		}

		/**
		 * @return string
		 */
		public function getRole(): string {
			return $this->name;
		}
	}