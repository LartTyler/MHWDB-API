<?php
	namespace App\Contrib\Data;

	use App\Entity\ArmorSetBonusRank;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class ArmorSetBonusRankEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see ArmorSetBonusRank
	 */
	class ArmorSetBonusRankEntityData extends AbstractEntityData {
		/**
		 * @var int
		 */
		protected $pieces;

		/**
		 * @var SimpleSkillRankEntityData
		 */
		protected $skill;

		/**
		 * ArmorSetBonusRankEntityData constructor.
		 *
		 * @param int $pieces
		 */
		protected function __construct(int $pieces) {
			$this->pieces = $pieces;
		}

		/**
		 * @return int
		 */
		public function getPieces(): int {
			return $this->pieces;
		}

		/**
		 * @param int $pieces
		 *
		 * @return $this
		 */
		public function setPieces(int $pieces) {
			$this->pieces = $pieces;

			return $this;
		}

		/**
		 * @return SimpleSkillRankEntityData
		 */
		public function getSkill(): SimpleSkillRankEntityData {
			return $this->skill;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'pieces' => $this->getPieces(),
				'skill' => $this->getSkill()->normalize(),
			];
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'pieces'))
				$this->setPieces($data->pieces);

			if (ObjectUtil::isset($data, 'skill'))
				$this->getSkill()->update($data->skill);
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function doFromJson(object $source) {
			$data = new static($source->pieces);
			$data->skill = SimpleSkillRankEntityData::fromJson($source->skill);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function doFromEntity(EntityInterface $entity) {
			if (!($entity instanceof ArmorSetBonusRank))
				throw static::createLoadFailedException(ArmorSetBonusRank::class);

			$data = new static($entity->getPieces());
			$data->skill = SimpleSkillRankEntityData::fromEntity($entity->getSkill());

			return $data;
		}
	}