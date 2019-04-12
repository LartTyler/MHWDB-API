<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="charms")
	 *
	 * Class Charm
	 *
	 * @package App\Entity
	 */
	class Charm implements EntityInterface, LengthCachingEntityInterface {
		use EntityTrait;

		/**
		 * @Assert\NotBlank()
		 *
		 * @ORM\Column(type="string", length=64, unique=true)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\CharmRank", mappedBy="charm", orphanRemoval=true, cascade={"all"})
		 * @ORM\OrderBy(value={"level": "ASC"})
		 *
		 * @var Collection|Selectable|CharmRank[]
		 */
		private $ranks;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "ranks.length"
		 */
		private $ranksLength = 0;

		/**
		 * Charm constructor.
		 *
		 * @param string $name
		 */
		public function __construct(string $name) {
			$this->name = $name;
			$this->ranks = new ArrayCollection();
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function setName(string $name) {
			$this->name = $name;

			return $this;
		}

		/**
		 * @return CharmRank[]|Collection|Selectable
		 */
		public function getRanks() {
			return $this->ranks;
		}

		/**
		 * @param int $level
		 *
		 * @return CharmRank|null
		 */
		public function getRank(int $level): ?CharmRank {
			$matches = $this->getRanks()->matching(
				Criteria::create()
					->where(Criteria::expr()->eq('level', $level))
					->setMaxResults(1)
			);

			if ($matches->count())
				return $matches->first();

			return null;
		}

		/**
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->ranksLength = $this->ranks->count();
		}
	}