<?php
	namespace App\Entity\Quests;

	use App\Game\Quest\Objective;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 */
	class HuntQuest extends AbstractMonsterTargetQuest {
		/**
		 * {@inheritdoc}
		 */
		protected $objective = Objective::HUNT;
	}