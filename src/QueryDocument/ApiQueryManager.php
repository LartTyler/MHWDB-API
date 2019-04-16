<?php
	namespace App\QueryDocument;

	use App\Entity\Armor;
	use App\Entity\ArmorAssets;
	use App\Entity\ArmorCraftingInfo;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use App\Entity\Charm;
	use App\Entity\CharmRank;
	use App\Entity\CharmRankCraftingInfo;
	use App\Entity\Decoration;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\MotionValue;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use App\Entity\Weapon;
	use App\Entity\WeaponCraftingInfo;
	use DaybreakStudios\DoctrineQueryDocument\QueryManager;
	use Doctrine\Common\Persistence\ObjectManager;

	class ApiQueryManager extends QueryManager {
		/**
		 * {@inheritdoc}
		 */
		public function __construct(ObjectManager $objectManager, array $operators = [], $useBuiltin = true) {
			parent::__construct($objectManager, $operators, $useBuiltin);

			$this->setAllMappedFields(
				[
					Armor::class => [
						'skills.length' => 'skillsLength',
						'slots.length' => 'slotsLength',
					],
					ArmorAssets::class => [
						'imageMale' => 'imageMale.uri',
						'imageFemale' => 'imageFemale.uri',
					],
					ArmorCraftingInfo::class => [
						'materials.length' => 'materialsLength',
					],
					ArmorSet::class => [
						'pieces.length' => 'piecesLength',
					],
					ArmorSetBonus::class => [
						'ranks.length' => 'ranksLength',
					],
					Charm::class => [
						'ranks.length' => 'ranksLength',
					],
					CharmRank::class => [
						'skills.length' => 'skillsLength',
					],
					CharmRankCraftingInfo::class => [
						'materials.length' => 'materialsLength',
					],
					Decoration::class => [
						'skills.length' => 'skillsLength',
					],
					Location::class => [
						'camps.length' => 'campsLength',
					],
					Monster::class => [
						'ailments.length' => 'ailmentsLength',
						'locations.length' => 'locationsLength',
						'elements.length' => 'elementsLength',
					],
					MotionValue::class => [
						'hits.length' => 'hitsLength',
					],
					Skill::class => [
						'ranks.length' => 'ranksLength',
					],
					SkillRank::class => [
						'skillName' => 'skill.name',
					],
					Weapon::class => [
						'elements.length' => 'elementsLength',
						'slots.length' => 'slotsLength',
						'durability.length' => 'durabilityLength',
					],
					WeaponCraftingInfo::class => [
						'branches.length' => 'branchesLength',
						'craftingMaterials.length' => 'craftingMaterialsLength',
						'upgradeMaterials.length' => 'upgradeMaterialsLength',
					],
				]
			);
		}
	}