<?php
	namespace App\Entity\Quests;

	use App\Game\Quest\Objective;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 */
	class CaptureQuest extends AbstractMonsterTargetQuest {
		/**
		 * {@inheritdoc}
		 */
		protected $objective = Objective::CAPTURE;
	}