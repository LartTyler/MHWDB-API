<?php
	namespace App\Export\Exporters;

	use App\Contrib\Data\ItemEntityData;
	use App\Entity\Item;
	use App\Export\Export;

	class ItemExporter extends AbstractExporter {
		/**
		 * ItemExporter constructor.
		 */
		public function __construct() {
			parent::__construct(Item::class);
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Item))
				throw new \InvalidArgumentException('$object must be an instance of ' . Item::class);

			$output = ItemEntityData::fromEntity($object)->normalize();

			return new Export('items', $output);
		}
	}