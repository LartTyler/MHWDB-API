<?php
	namespace App\Entity;

	use App\Entity\Strings\AilmentStrings;
	use App\Localization\TranslatableEntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="ailments")
	 *
	 * Class Ailment
	 *
	 * @package App\Entity
	 */
	class Ailment implements EntityInterface, TranslatableEntityInterface {
		use EntityTrait;

		/**
		 * @Assert\Valid()
		 *
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
		 * @Assert\Valid()
		 *
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
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\AilmentStrings",
		 *     mappedBy="ailment",
		 *     orphanRemoval=true,
		 *     cascade={"all"},
		 *     fetch="EAGER"
		 * )
		 *
		 * @var Collection|Selectable|AilmentStrings[]
		 */
		private $strings;

		/**
		 * Ailment constructor.
		 */
		public function __construct() {
			$this->recovery = new AilmentRecovery($this);
			$this->protection = new AilmentProtection($this);

			$this->strings = new ArrayCollection();
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

		/**
		 * @return AilmentStrings[]|Collection|Selectable
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return AilmentStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new AilmentStrings($this, $language));

			return $strings;
		}
	}