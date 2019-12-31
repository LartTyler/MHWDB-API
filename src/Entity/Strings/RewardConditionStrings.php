<?php
	namespace App\Entity\Strings;

	use App\Entity\RewardCondition;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="reward_condition_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"reward_condition_id", "language"})}
	 * )
	 */
	class RewardConditionStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\RewardCondition", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var RewardCondition
		 */
		private $rewardCondition;

		/**
		 * @Assert\Length(max="128")
		 *
		 * @ORM\Column(type="string", length=128, nullable=true)
		 *
		 * @var string|null
		 */
		private $subtype = null;

		/**
		 * RewardConditionStrings constructor.
		 *
		 * @param RewardCondition $rewardCondition
		 * @param string          $language
		 */
		public function __construct(RewardCondition $rewardCondition, string $language) {
			$this->rewardCondition = $rewardCondition;
			$this->language = $language;
		}

		/**
		 * @return string|null
		 */
		public function getSubtype(): ?string {
			return $this->subtype;
		}

		/**
		 * @param string|null $subtype
		 *
		 * @return $this
		 */
		public function setSubtype(?string $subtype) {
			$this->subtype = $subtype;

			return $this;
		}
	}