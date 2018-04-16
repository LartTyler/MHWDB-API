<?php
	namespace App\QueryDocument;

	use App\Entity\ArmorAssets;
	use App\Entity\SkillRank;
	use DaybreakStudios\DoctrineQueryDocument\QueryManager;
	use Doctrine\Common\Persistence\ObjectManager;

	class ApiQueryManager extends QueryManager {
		/**
		 * {@inheritdoc}
		 */
		public function __construct(ObjectManager $objectManager, array $operators = [], $useBuiltin = true) {
			parent::__construct($objectManager, $operators, $useBuiltin);

			$this->setAllMappedFields([
				ArmorAssets::class => [
					'imageMale' => 'imageMale.uri',
					'imageFemale' => 'imageFemale.uri',
				],
				SkillRank::class => [
					'skillName' => 'skill.name',
				],
			]);
		}
	}