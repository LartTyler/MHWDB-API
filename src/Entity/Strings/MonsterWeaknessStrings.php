<?php
	namespace App\Entity\Strings;

	use App\Entity\MonsterWeakness;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity(readOnly=true)
	 * @ORM\Table(
	 *     name="monster_weakness_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"monster_weakness_id", "language"})}
	 * )
	 */
	class MonsterWeaknessStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\MonsterWeakness", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var MonsterWeakness
		 */
		private $monsterWeakness;

		/**
		 * @ORM\Column(type="text", nullable=true, name="_condition")
		 *
		 * @var string|null
		 */
		private $condition = null;

		/**
		 * MonsterWeaknessStrings constructor.
		 *
		 * @param MonsterWeakness $monsterWeakness
		 * @param string          $language
		 */
		public function __construct(MonsterWeakness $monsterWeakness, string $language) {
			$this->monsterWeakness = $monsterWeakness;
			$this->language = $language;
		}

		/**
		 * @return string|null
		 */
		public function getCondition(): ?string {
			return $this->condition;
		}

		/**
		 * @param string|null $condition
		 *
		 * @return $this
		 */
		public function setCondition(?string $condition) {
			$this->condition = $condition;

			return $this;
		}
	}