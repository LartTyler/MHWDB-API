<?php
	namespace App\Entity\Quests;

	use App\Game\Quest\DeliveryTarget;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 */
	class ObjectDeliveryQuest extends DeliveryQuest {
		/**
		 * {@inheritdoc}
		 */
		protected $targetType = DeliveryTarget::OBJECT;
	}