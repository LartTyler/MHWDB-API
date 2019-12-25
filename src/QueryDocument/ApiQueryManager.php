<?php
	namespace App\QueryDocument;

	use App\Entity\Ailment;
	use App\Entity\Armor;
	use App\Entity\ArmorAssets;
	use App\Entity\ArmorSet;
	use App\Entity\SkillRank;
	use DaybreakStudios\DoctrineQueryDocument\QueryManager;
	use Doctrine\ORM\EntityManagerInterface;

	class ApiQueryManager extends QueryManager {
		/**
		 * {@inheritdoc}
		 */
		public function __construct(EntityManagerInterface $entityManager, array $operators = [], $useBuiltin = true) {
			parent::__construct($entityManager, $operators, $useBuiltin);

			$this->setAllMappedFields(
				[
					Ailment::class => [
						'name' => 'strings.name',
						'description' => 'strings.description',
					],
					Armor::class => [
						'name' => 'strings.name',
					],
					ArmorSet::class => [
						'name' => 'strings.name',
					],
					ArmorAssets::class => [
						'imageMale' => 'imageMale.uri',
						'imageFemale' => 'imageFemale.uri',
					],
					SkillRank::class => [
						'skillName' => 'skill.name',
					],
				]
			);
		}
	}