<?php
	namespace App\Export\Exporters;

	use App\Contrib\Data\AilmentEntityData;
	use App\Entity\Ailment;
	use App\Export\Export;
	use App\Export\ExportHelper;

	class AilmentExporter extends AbstractExporter {
		/**
		 * @var ExportHelper
		 */
		protected $helper;

		/**
		 * AilmentExporter constructor.
		 *
		 * @param ExportHelper $helper
		 */
		public function __construct(ExportHelper $helper) {
			parent::__construct(Ailment::class);
			$this->helper = $helper;
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Ailment))
				throw new \InvalidArgumentException('$object must be an instance of ' . Ailment::class);

			$output = AilmentEntityData::fromEntity($object)->normalize();

			return new Export('ailments', $output);
		}
	}