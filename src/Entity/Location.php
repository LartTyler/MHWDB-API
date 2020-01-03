<?php
	namespace App\Entity;

	use App\Entity\Strings\LocationStrings;
	use App\Localization\TranslatableEntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
	 * @ORM\Table(name="locations")
	 *
	 * Class Location
	 *
	 * @package App\Entity
	 */
	class Location implements EntityInterface, TranslatableEntityInterface, LengthCachingEntityInterface {
		use EntityTrait;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Range(min=1)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $zoneCount;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\Camp", mappedBy="location", orphanRemoval=true, cascade={"all"})
		 *
		 * @var Camp[]|Collection|Selectable
		 */
		private $camps;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\LocationStrings",
		 *     mappedBy="location",
		 *     orphanRemoval=true,
		 *     cascade={"all"},
		 *     fetch="EAGER"
		 * )
		 *
		 * @var Collection|Selectable|LocationStrings[]
		 */
		private $strings;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "camps.length"
		 */
		private $campsLength = 0;

		/**
		 * Location constructor.
		 *
		 * @param int $zoneCount
		 */
		public function __construct(int $zoneCount) {
			$this->zoneCount = $zoneCount;

			$this->camps = new ArrayCollection();
			$this->strings = new ArrayCollection();
		}

		/**
		 * @return int
		 */
		public function getZoneCount(): int {
			return $this->zoneCount;
		}

		/**
		 * @param int $zoneCount
		 *
		 * @return $this
		 */
		public function setZoneCount(int $zoneCount) {
			$this->zoneCount = $zoneCount;

			return $this;
		}

		/**
		 * @return Camp[]|Collection|Selectable
		 */
		public function getCamps() {
			return $this->camps;
		}

		/**
		 * @param int $zone
		 *
		 * @return Camp|null
		 */
		public function getCamp(int $zone): ?Camp {
			$matching = $this->getCamps()->matching(
				Criteria::create()
					->where(Criteria::expr()->eq('zone', $zone))
					->setMaxResults(1)
			);

			if (!$matching->count())
				return null;

			return $matching->first();
		}

		/**
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->campsLength = $this->camps->count();
		}

		/**
		 * @return Collection|Selectable|LocationStrings[]
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return LocationStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new LocationStrings($this, $language));

			return $strings;
		}
	}