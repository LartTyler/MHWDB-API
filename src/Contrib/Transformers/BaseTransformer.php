<?php
	namespace App\Contrib\Transformers;

	use App\Entity\CraftingMaterialCost;
	use App\Entity\Item;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use DaybreakStudios\Utility\EntityTransformers\AbstractEntityTransformer;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\IntegrityException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;
	use DaybreakStudios\Utility\EntityTransformers\Utility\ObjectUtil;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	abstract class BaseTransformer extends AbstractEntityTransformer {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * AbstractTransformer constructor.
		 *
		 * @param EntityManagerInterface  $entityManager
		 * @param ValidatorInterface|null $validator
		 */
		public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator = null) {
			parent::__construct($entityManager, $validator);
		}

		/**
		 * @param string $class
		 *
		 * @return \InvalidArgumentException
		 * @deprecated
		 */
		protected function createEntityNotSupportedException(string $class): \InvalidArgumentException {
			return new \InvalidArgumentException('This transformer does not support transforming ' . $class);
		}

		/**
		 * @param string   $prefix
		 * @param int      $index
		 * @param string[] $keys
		 *
		 * @return ValidationException
		 * @deprecated
		 */
		protected function createMissingArrayFieldsException(
			string $prefix,
			int $index,
			array $keys
		): ValidationException {
			return ValidationException::missingFields(
				array_map(
					function(string $key) use ($prefix, $index): string {
						return $prefix . '[' . $index . '].' . $key;
					},
					$keys
				)
			);
		}

		/**
		 * @param string     $path
		 * @param Collection $collection
		 * @param object[]   $ranks
		 *
		 * @return void
		 */
		protected function populateFromSimpleSkillsArray(string $path, Collection $collection, array $ranks): void {
			$collection->clear();

			foreach ($ranks as $index => $rank) {
				$skillRank = $this->getSkillRankFromSimpleSkill($path . '[' . $index . ']', $rank);

				$collection->add($skillRank);
			}
		}

		/**
		 * @param string $path
		 * @param object $definition
		 *
		 * @return SkillRank
		 */
		protected function getSkillRankFromSimpleSkill(string $path, object $definition): SkillRank {
			$missing = ObjectUtil::getMissingProperties(
				$definition,
				[
					'skill',
					'level',
				]
			);

			if ($missing) {
				throw ValidationException::missingFields(
					array_map(
						function(string $key) use ($path): string {
							return $path . '.' . $key;
						},
						$missing
					)
				);
			}

			$skill = $this->entityManager->getRepository(Skill::class)->find($definition->skill);

			if (!$skill)
				throw IntegrityException::missingReference($path . '.skill', 'Skill');

			$skillRank = $skill->getRank($definition->level);

			if (!$skillRank)
				throw IntegrityException::missingReference($path . '.level', 'SkillRank');

			return $skillRank;
		}

		/**
		 * @param string     $path
		 * @param Collection $collection
		 * @param object[]   $costs
		 *
		 * @return void
		 */
		protected function populateFromSimpleCostArray(string $path, Collection $collection, array $costs): void {
			$collection->clear();

			foreach ($costs as $index => $cost) {
				$missing = ObjectUtil::getMissingProperties(
					$cost,
					[
						'item',
						'quantity',
					]
				);

				if ($missing)
					throw ValidationException::missingNestedFields($path, $index, $missing);

				$item = $this->entityManager->getRepository(Item::class)->find($cost->item);

				if (!$item)
					throw IntegrityException::missingReference($path . '[' . $index . '].item', 'Item');

				$collection->add(new CraftingMaterialCost($item, $cost->quantity));
			}
		}
	}