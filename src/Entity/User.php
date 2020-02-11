<?php
	namespace App\Entity;

	use App\Security\Role;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Security\Core\User\UserInterface;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="users")
	 * Class User
	 *
	 * @package App\Entity
	 */
	class User implements EntityInterface, UserInterface {
		use EntityTrait;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Email()
		 *
		 * @ORM\Column(type="string", length=254, unique=true)
		 * @var string
		 */
		private $email;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Length(min=3)
		 *
		 * @ORM\Column(type="string", length=32, unique=true)
		 * @var string
		 */
		private $displayName;

		/**
		 * @ORM\Column(type="datetime_immutable")
		 *
		 * @var \DateTimeImmutable
		 */
		private $createdDate;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\UserRole", mappedBy="user", orphanRemoval=true, cascade={"all"})
		 *
		 * @var UserRole[]|Collection|Selectable
		 */
		private $roles;

		/**
		 * @ORM\Column(type="boolean")
		 *
		 * @var bool
		 */
		private $disabled = false;

		/**
		 * @ORM\Column(type="string", length=64, nullable=true)
		 *
		 * @var string|null
		 */
		private $password = null;

		/**
		 * @ORM\Column(type="string", length=64, nullable=true)
		 *
		 * @var string|null
		 */
		private $activationCode = null;

		/**
		 * @ORM\Column(type="string", length=64, nullable=true)
		 *
		 * @var string|null
		 */
		private $passwordResetCode = null;

		/**
		 * User constructor.
		 *
		 * @param string $email
		 * @param string $displayName
		 */
		public function __construct(string $email, string $displayName) {
			$this->email = $email;
			$this->displayName = $displayName;

			$this->createdDate = new \DateTimeImmutable();
			$this->roles = new ArrayCollection();
		}

		/**
		 * @return string
		 */
		public function getEmail(): string {
			return $this->email;
		}

		/**
		 * @param string $email
		 *
		 * @return $this
		 */
		public function setEmail(string $email) {
			$this->email = $email;

			return $this;
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
		 * @return \DateTimeImmutable
		 */
		public function getCreatedDate(): \DateTimeImmutable {
			return $this->createdDate;
		}

		/**
		 * @return bool
		 */
		public function isDisabled(): bool {
			return $this->disabled;
		}

		/**
		 * @param bool $disabled
		 *
		 * @return $this
		 */
		public function setDisabled(bool $disabled) {
			$this->disabled = $disabled;

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
		 * @Assert\All(
		 *     @Assert\Choice(callback={"App\Security\Role", "values"})
		 * )
		 *
		 * @return string[]
		 */
		public function getRoles(): array {
			return $this->roles->map(
				function(UserRole $role) {
					return $role->getRole();
				}
			)->toArray();
		}

		/**
		 * @param string[] $roles
		 *
		 * @return $this
		 * @see Role
		 */
		public function setRoles(array $roles) {
			foreach ($this->getRoles() as $role) {
				if (!in_array($role, $roles))
					$this->revokeRole($role);
			}

			foreach ($roles as $role)
				$this->grantRole($role);

			return $this;
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

		/**
		 * @return string|null
		 */
		public function getActivationCode(): ?string {
			return $this->activationCode;
		}

		/**
		 * @param string|null $activationCode
		 *
		 * @return $this
		 */
		public function setActivationCode(?string $activationCode) {
			$this->activationCode = $activationCode;

			return $this;
		}

		/**
		 * @return string|null
		 */
		public function getPasswordResetCode(): ?string {
			return $this->passwordResetCode;
		}

		/**
		 * @param string|null $passwordResetCode
		 *
		 * @return $this
		 */
		public function setPasswordResetCode(?string $passwordResetCode) {
			$this->passwordResetCode = $passwordResetCode;

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
	}