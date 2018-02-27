<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;

	class Weapon implements EntityInterface, SluggableInterface {
		use EntityTrait;
		use SluggableTrait;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var string
		 */
		private $type;

		/**
		 * @var int
		 */
		private $rarity;

		/**
		 * @var Collection|Selectable|ItemAttribute[]
		 */
		private $attributes;

		/**
		 * Weapon constructor.
		 *
		 * @param string $name
		 * @param string $type
		 * @param int    $rarity
		 */
		public function __construct(string $name, string $type, int $rarity) {
			$this->name = $name;
			$this->type = $type;
			$this->rarity = $rarity;

			$this->attributes = new ArrayCollection();

			$this->setSlug($name);
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @return int
		 */
		public function getRarity(): int {
			return $this->rarity;
		}

		/**
		 * @param int $rarity
		 *
		 * @return $this
		 */
		public function setRarity(int $rarity): Weapon {
			$this->rarity = $rarity;

			return $this;
		}

		/**
		 * @return Collection|Selectable|ItemAttribute[]
		 */
		public function getAttributes() {
			return $this->attributes;
		}

		/**
		 * @param string $attribute
		 *
		 * @return ItemAttribute|null
		 */
		protected function getAttributeObject(string $attribute): ?ItemAttribute {
			$criteria = Criteria::create()
				->where(Criteria::expr()->eq('name', $attribute))
				->setMaxResults(1);

			$matching = $this->getAttributes()->matching($criteria);

			if (!$matching->count())
				return null;

			return $matching->first();
		}

		/**
		 * @param string $attribute
		 *
		 * @return mixed|null
		 */
		public function getAttribute(string $attribute) {
			$attr = $this->getAttributeObject($attribute);

			if ($attr)
				return $attr->getValue();

			return null;
		}

		/**
		 * @param string $attribute
		 * @param mixed  $value
		 *
		 * @return Weapon
		 */
		public function setAttribute(string $attribute, $value): Weapon {
			if ($attr = $this->getAttributeObject($attribute))
				$attr->setValue($value);
			else
				$this->getAttributes()->add(new ItemAttribute($attribute, $value));

			return $this;
		}
	}