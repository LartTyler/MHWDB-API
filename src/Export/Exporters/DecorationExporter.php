<?php
	namespace App\Export\Exporters;

	use App\Contrib\Data\DecorationEntityData;
	use App\Entity\Decoration;
	use App\Export\Export;

	class DecorationExporter extends AbstractExporter {
		/**
		 * DecorationExporter constructor.
		 */
		public function __construct() {
			parent::__construct(Decoration::class);
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Decoration))
				throw new \InvalidArgumentException('$object must be an instance of ' . Decoration::class);

			$output = DecorationEntityData::fromEntity($object)->normalize();

			return new Export('decorations', $output);
		}
	}