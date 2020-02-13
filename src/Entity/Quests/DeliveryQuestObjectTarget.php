<?php
	namespace App\Entity\Quests;

	use App\Game\Quest\DeliveryType;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 */
	class DeliveryQuestObjectTarget extends DeliveryQuestTarget {
		/**
		 * {@inheritdoc}
		 */
		protected $deliveryType = DeliveryType::OBJECT;
	}