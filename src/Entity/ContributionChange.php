<?php
	namespace App\Entity;

	use App\Contrib\ContributionType;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="contribution_changes")
	 *
	 * Class ContributionChange
	 *
	 * @package App\Entity
	 */
	class ContributionChange implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Contribution", inversedBy="changes")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Contribution
		 */
		private $contribution;

		/**
		 * @ORM\Column(type="string", length=16)
		 *
		 * @var string
		 * @see ContributionType
		 */
		private $type;

		/**
		 * @ORM\Column(type="json", nullable=true)
		 *
		 * @var array|null
		 */
		private $data = null;

		/**
		 * ContributionChange constructor.
		 *
		 * @param Contribution $contribution
		 * @param string       $type
		 */
		public function __construct(Contribution $contribution, string $type) {
			$this->contribution = $contribution;
			$this->type = $type;
		}

		/**
		 * @return Contribution
		 */
		public function getContribution(): Contribution {
			return $this->contribution;
		}

		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}

		/**
		 * @return array|null
		 */
		public function getData(): ?array {
			return $this->data;
		}

		/**
		 * @param array|null $data
		 *
		 * @return $this
		 */
		public function setData(?array $data) {
			$this->data = $data;

			return $this;
		}
	}