<?php
	namespace App\Entity\Strings;

	use App\Entity\Quest;
	use App\Entity\Quests\ObjectDeliveryQuest;
	use App\Localization\StringsEntityInterface;
	use App\Localization\StringsEntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="quest_strings")
	 */
	class QuestStrings implements StringsEntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Quest", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Quest
		 */
		private $quest;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Length(max="128")
		 *
		 * @ORM\Column(type="string", length=128, unique=true)
		 *
		 * @var string
		 */
		private $name = null;

		/**
		 * @Assert\NotBlank()
		 *
		 * @ORM\Column(type="text")
		 *
		 * @var string
		 */
		private $description = null;

		/**
		 * Only used by {@see ObjectDeliveryQuest} to specify the name of the quest's target object.
		 *
		 * @ORM\Column(type="string", length=64, nullable=true)
		 *
		 * @var string|null
		 */
		private $objectName = null;

		/**
		 * QuestStrings constructor.
		 *
		 * @param Quest  $quest
		 * @param string $language
		 */
		public function __construct(Quest $quest, string $language) {
			$this->quest = $quest;
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

		/**
		 * @return string|null
		 */
		public function getObjectName(): ?string {
			return $this->objectName;
		}

		/**
		 * @param string|null $objectName
		 *
		 * @return $this
		 */
		public function setObjectName(?string $objectName) {
			$this->objectName = $objectName;

			return $this;
		}
	}