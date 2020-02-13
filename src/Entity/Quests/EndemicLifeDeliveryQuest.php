<?php
	namespace App\Entity\Quests;

	use App\Entity\EndemicLife;
	use App\Game\Quest\DeliveryTarget;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 */
	class EndemicLifeDeliveryQuest extends DeliveryQuest {
		/**
		 * {@inheritdoc}
		 */
		protected $targetType = DeliveryTarget::ENDEMIC_LIFE;

		/**
		 * @Assert\NotNull()
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\EndemicLife")
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