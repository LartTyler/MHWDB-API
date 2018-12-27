<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity(repositoryClass="App\Repository\ArmorSetBonusRankRepository")
	 * @ORM\Table(name="armor_set_bonus_ranks")
	 *
	 * Class ArmorSetBonusRank
	 *
	 * @package App\Entity
	 */
	class ArmorSetBonusRank implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\ArmorSetBonus", inversedBy="ranks")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var ArmorSetBonus
		 */
		private $bonus;

		/**
		 * @Assert\Range(min=1)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $pieces;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\SkillRank")
		 * @ORM\JoinColumn(nullable=false)
		 *
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