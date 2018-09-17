<?php
	namespace App\Import\Importers;

	use App\Entity\Item;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class ItemImporter extends AbstractImporter {
		/**
		 * ItemImporter constructor.
		 */
		public function __construct() {
			parent::__construct(Item::class);
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function import(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Item))
				throw $this->createCannotImportException();

			$entity
				->setName($data->name)
				->setDescription($data->description)
				->setRarity($data->rarity)
				->setCarryLimit($data->carryLimit)
				->setValue($data->value);
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(object $data): EntityInterface {
			$item = new Item($data->name, $data->description, $data->rarity);

			$this->import($item, $data);

			return $item;
		}
	}