<?php
	namespace App\Export\Exporters;

	use App\Contrib\Data\MotionValueEntityData;
	use App\Entity\MotionValue;
	use App\Export\Export;

	class MotionValueExporter extends AbstractExporter {
		/**
		 * MotionValueExporter constructor.
		 */
		public function __construct() {
			parent::__construct(MotionValue::class);
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof MotionValue))
				throw new \InvalidArgumentException('$object must be an instance of ' . MotionValue::class);

			$output = MotionValueEntityData::fromEntity($object)->normalize();

			return new Export('motion-values/' . $object->getWeaponType(), $output);
		}
	}