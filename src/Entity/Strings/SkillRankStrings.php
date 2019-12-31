<?php
	namespace App\Entity\Strings;

	use App\Entity\SkillRank;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="skill_rank_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"skill_rank_id", "language"})}
	 * )
	 */
	class SkillRankStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\SkillRank", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var SkillRank
		 */
		private $rank;

		/**
		 * @ORM\Column(type="text")
		 *
		 * @var string
		 */
		private $description;

		/**
		 * SkillRankStrings constructor.
		 *
		 * @param SkillRank $rank
		 * @param string    $language
		 */
		public function __construct(SkillRank $rank, string $language) {
			$this->rank = $rank;
			$this->language = $language;
		}

		/**
		 * @return string
		 */
		public function getDescription(): string {
			return $this->description;
		}

		/**
		 * @param string $description
		 *
		 * @return $this
		 */
		public function setDescription(string $description) {
			$this->description = $description;

			return $this;
		}
	}