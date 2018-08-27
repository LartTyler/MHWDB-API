<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="locations")
	 *
	 * Class Location
	 *
	 * @package App\Entity
	 */
	class Location implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\Column(type="string", length=32, unique=true)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $zoneCount;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\Camp", mappedBy="location", orphanRemoval=true, cascade={"all"})
		 *
		 * @var Camp[]|Collection|Selectable
		 */
		private $camps;

		/**
		 * Location constructor.
		 *
		 * @param string $name
		 * @param int    $zoneCount
		 */
		public function __construct(string $name, int $zoneCount) {
			$this->name = $name;
			$this->zoneCount = $zoneCount;

			$this->camps = new ArrayCollection();
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
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
	}