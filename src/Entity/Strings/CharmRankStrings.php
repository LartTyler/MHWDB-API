<?php
	namespace App\Entity\Strings;

	use App\Entity\CharmRank;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="charm_rank_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"charm_rank_id", "language"})}
	 * )
	 */
	class CharmRankStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\CharmRank", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var CharmRank
		 */
		private $charmRank;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Length(max="64")
		 *
		 * @ORM\Column(type="string", length=64, unique=true)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * CharmRankStrings constructor.
		 *
		 * @param CharmRank $charmRank
		 * @param string    $language
		 */
		public function __construct(CharmRank $charmRank, string $language) {
			$this->charmRank = $charmRank;
			$this->language = $language;
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function setName(string $name) {
			$this->name = $name;

			return $this;
		}
	}