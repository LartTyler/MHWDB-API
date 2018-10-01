<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\VeritasBundle\Security\Core\User\VeritasUserInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Security\Core\User\UserInterface;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="users")
	 * Class User
	 *
	 * @package App\Entity
	 */
	class User implements EntityInterface, UserInterface, VeritasUserInterface {
		use EntityTrait;

		/**
		 * @ORM\Column(type="string", length=254, unique=true)
		 * @var string
		 */
		private $email;

		/**
		 * @ORM\Column(type="string", length=32, unique=true)
		 * @var string
		 */
		private $displayName;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\UserRole", mappedBy="user", orphanRemoval=true, cascade={"all"})
		 *
		 * @var UserRole[]|Collection|Selectable
		 */
		private $roles;

		/**
		 * @ORM\Column(type="string", length=64)
		 * @var string|null
		 */
		private $password = null;

		/**
		 * User constructor.
		 *
		 * @param string $email
		 * @param string $displayName
		 */
		public function __construct(string $email, string $displayName) {
			$this->email = $email;
			$this->displayName = $displayName;

			$this->roles = new ArrayCollection();
		}

		/**
		 * @return int|null
		 */
		public function getSubjectIdentifier(): ?int {
			return $this->getId();
		}

		/**
		 * @return string
		 */
		public function getEmail(): string {
			return $this->email;
		}

		/**
		 * Returns the identifier of the user.
		 *
		 * This method's name is completely misleading, but it has to be called this to work with Symfony's security
		 * system.
		 *
		 * @return string
		 */
		public function getUsername(): string {
			return $this->getEmail();
		}

		/**
		 * @return string
		 */
		public function getDisplayName(): string {
			return $this->displayName;
		}

		/**
		 * @param string $displayName
		 *
		 * @return $this
		 */
		public function setDisplayName(string $displayName) {
			$this->displayName = $displayName;

			return $this;
		}

		/**
		 * @return null|string
		 */
		public function getPassword(): ?string {
			return $this->password;
		}

		/**
		 * @param string $password
		 *
		 * @return $this
		 */
		public function setPassword(string $password) {
			$this->password = $password;

			return $this;
		}

		/**
		 * @return array
		 */
		public function getRoles(): array {
			return $this->roles->toArray();
		}

		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasRole(string $name): bool {
			return $this->findRole($name) !== null;
		}

		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function grantRole(string $name) {
			if ($this->hasRole($name))
				return $this;

			$this->roles->add(new UserRole($this, $name));

			return $this;
		}

		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function revokeRole(string $name) {
			$role = $this->findRole($name);

			if ($role)
				$this->roles->removeElement($role);

			return $this;
		}

		/**
		 * @param string $name
		 *
		 * @return UserRole|null
		 */
		private function findRole(string $name): ?UserRole {
			$match = $this->roles->matching(
				Criteria::create()
					->where(Criteria::expr()->eq('name', $name))
			);

			if (!$match->count())
				return null;

			return $match->first();
		}

		/**
		 * @return null
		 */
		public function getSalt() {
			return null;
		}

		/**
		 * @return void
		 */
		public function eraseCredentials() {
			// noop
		}
	}