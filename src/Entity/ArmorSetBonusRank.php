<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class ArmorSetBonusRank implements EntityInterface {
		use EntityTrait;

		/**
		 * @var ArmorSetBonus
		 */
		private $bonus;

		/**
		 * @var int
		 */
		private $pieces;

		/**
		 * @var SkillRank
		 */
		private $skill;

		/**
		 * ArmorSetBonusRank constructor.
		 *
		 * @param ArmorSetBonus $bonus
		 * @param int           $pieces
		 * @param SkillRank     $skill
		 */
		public function __construct(ArmorSetBonus $bonus, int $pieces, SkillRank $skill) {
			$this->bonus = $bonus;
			$this->pieces = $pieces;
			$this->skill = $skill;
		}

		/**
		 * @return ArmorSetBonus
		 */
		public function getBonus(): ArmorSetBonus {
			return $this->bonus;
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
		 * @return SkillRank
		 */
		public function getSkill(): SkillRank {
			return $this->skill;
		}

		/**
		 * @param SkillRank $skill
		 *
		 * @return $this
		 */
		public function setSkill(SkillRank $skill) {
			$this->skill = $skill;

			return $this;
		}
	}