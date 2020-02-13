<?php
	namespace App\Entity\Quests;

	use App\Entity\EndemicLife;
	use App\Game\Quest\DeliveryType;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 */
	class DeliveryQuestEndemicLifeTarget extends DeliveryQuestTarget {
		/**
		 * {@inheritdoc}
		 */
		protected $deliveryType = DeliveryType::ENDEMIC_LIFE;

		/**
		 * @Assert\NotNull()
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\EndemicLife", fetch="EAGER")
		 *
		 * @var EndemicLife|null
		 */
		private $endemicLife = null;

		/**
		 * @return EndemicLife
		 */
		public function getEndemicLife(): EndemicLife {
			return $this->endemicLife;
		}

		/**
		 * @param EndemicLife $endemicLife
		 *
		 * @return $this
		 */
		public function setEndemicLife(EndemicLife $endemicLife) {
			$this->endemicLife = $endemicLife;

			return $this;
		}
	}