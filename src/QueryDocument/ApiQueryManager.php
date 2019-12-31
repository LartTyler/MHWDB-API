<?php
	namespace App\QueryDocument;

	use App\Entity\Ailment;
	use App\Entity\Armor;
	use App\Entity\ArmorAssets;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use App\Entity\Camp;
	use App\Entity\Charm;
	use App\Entity\Decoration;
	use App\Entity\Item;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\MonsterWeakness;
	use App\Entity\MotionValue;
	use App\Entity\RewardCondition;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use App\Localization\QueryLocalizationHelper;
	use DaybreakStudios\DoctrineQueryDocument\QueryManager;
	use Doctrine\ORM\EntityManagerInterface;
	use Doctrine\ORM\QueryBuilder;

	class ApiQueryManager extends QueryManager {
		/**
		 * @var QueryLocalizationHelper
		 */
		protected $localizationHelper;

		/**
		 * {@inheritdoc}
		 */
		public function __construct(
			EntityManagerInterface $entityManager,
			QueryLocalizationHelper $localizationHelper,
			array $operators = [],
			$useBuiltin = true
		) {
			parent::__construct($entityManager, $operators, $useBuiltin);

			$this->localizationHelper = $localizationHelper;

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
					ArmorSetBonus::class => [
						'name' => 'strings.name',
					],
					ArmorAssets::class => [
						'imageMale' => 'imageMale.uri',
						'imageFemale' => 'imageFemale.uri',
					],
					Camp::class => [
						'name' => 'strings.name',
					],
					Charm::class => [
						'name' => 'strings.name',
					],
					Decoration::class => [
						'name' => 'strings.name',
					],
					Item::class => [
						'name' => 'strings.name',
						'description' => 'strings.description',
					],
					Location::class => [
						'name' => 'strings.name',
					],
					Monster::class => [
						'name' => 'strings.name',
						'description' => 'strings.description',
					],
					MonsterWeakness::class => [
						'condition' => 'strings.description',
					],
					MotionValue::class => [
						'name' => 'strings.name',
					],
					RewardCondition::class => [
						'subtype' => 'strings.subtype',
					],
					Skill::class => [
						'name' => 'strings.name',
					],
					SkillRank::class => [
						'skillName' => 'skill.strings.name',
					],
				]
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function apply(QueryBuilder $qb, array $query): void {
			$document = $this->create($qb);
			$document->process($query);

			$this->localizationHelper->addTranslationClauses($document->getResolver(), $qb, $query);
		}
	}