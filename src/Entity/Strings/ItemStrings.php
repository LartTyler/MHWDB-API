<?php
	namespace App\Entity\Strings;

	use App\Entity\Item;
	use App\Localization\StringsEntityTrait;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="item_strings",
	 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"item_id", "language"})}
	 * )
	 */
	class ItemStrings implements EntityInterface {
		use StringsEntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Item", inversedBy="strings")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Item
		 */
		private $item;

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
		 * @Assert\NotNull()
		 *
		 * @ORM\Column(type="text")
		 *
		 * @var string
		 */
		private $description;

		/**
		 * ItemStrings constructor.
		 *
		 * @param Item   $item
		 * @param string $language
		 */
		public function __construct(Item $item, string $language) {
			$this->item = $item;
			$this->language = $language;
		}

		/**
		 * @return Item
		 */
		public function getItem(): Item {
			return $this->item;
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
	}