<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="assets",
	 *     uniqueConstraints={
	 *         @ORM\UniqueConstraint(columns={"primary_hash", "secondary_hash"})
	 *     }
	 * )
	 *
	 * Class Asset
	 *
	 * @package App\Entity
	 */
	class Asset implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\Column(type="text")
		 *
		 * @var string
		 */
		private $uri;

		/**
		 * @ORM\Column(type="string", length=128)
		 *
		 * @var string
		 */
		private $primaryHash;

		/**
		 * @ORM\Column(type="string", length=128)
		 *
		 * @var string
		 */
		private $secondaryHash;

		/**
		 * Asset constructor.
		 *
		 * @param string $uri
		 * @param string $primaryHash
		 * @param string $secondaryHash
		 */
		public function __construct(string $uri, string $primaryHash, string $secondaryHash) {
			$this->uri = $uri;
			$this->primaryHash = $primaryHash;
			$this->secondaryHash = $secondaryHash;
		}

		/**
		 * @return string
		 */
		public function getUri(): string {
			return $this->uri;
		}

		/**
		 * @param string $uri
		 *
		 * @return $this
		 */
		public function setUri($uri) {
			$this->uri = $uri;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getPrimaryHash(): string {
			return $this->primaryHash;
		}

		/**
		 * @return string
		 */
		public function getSecondaryHash(): string {
			return $this->secondaryHash;
		}
	}