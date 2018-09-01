<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Security\Core\Role\Role;

	/**
	 * @ORM\Entity(readOnly=true)
	 * @ORM\Table(name="user_roles")
	 *
	 * Class UserRole
	 *
	 * @package App\Entity
	 */
	class UserRole extends Role implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="roles")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var User
		 */
		private $user;

		/**
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
			parent::__construct($name);

			$this->user = $user;
			$this->name = $name;
		}

		/**
		 * @return User
		 */
		public function getUser(): User {
			return $this->user;
		}
	}