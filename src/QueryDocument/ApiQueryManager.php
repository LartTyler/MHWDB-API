<?php
	namespace App\QueryDocument;

	use App\Entity\Ailment;
	use App\Entity\Armor;
	use App\Entity\ArmorAssets;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use App\Entity\Camp;
	use App\Entity\Charm;
	use App\Entity\Item;
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
					Item::class => [
						'name' => 'strings.name',
						'description' => 'strings.description',
					],
					SkillRank::class => [
						'skillName' => 'skill.name',
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