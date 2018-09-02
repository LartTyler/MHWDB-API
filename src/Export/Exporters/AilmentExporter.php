<?php
	namespace App\Export\Exporters;

	use App\Entity\Ailment;
	use App\Export\Export;
	use App\Export\ExportHelper;

	class AilmentExporter extends AbstractExporter {
		/**
		 * AilmentExporter constructor.
		 */
		public function __construct() {
			parent::__construct(Ailment::class);
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Ailment))
				throw new \InvalidArgumentException('$object must be an instance of ' . Ailment::class);

			$recovery = $object->getRecovery();
			$protection = $object->getProtection();

			$output = [
				'name' => $object->getName(),
				'description' => $object->getDescription(),
				'recovery' => [
					'actions' => $recovery->getActions(),
					'items' => ExportHelper::toReferenceArray($recovery->getItems()),
				],
				'protection' => [
					'items' => ExportHelper::toReferenceArray($protection->getItems()),
					'skills' => ExportHelper::toReferenceArray($protection->getSkills()),
				],
			];

			ksort($output);

			return new Export('ailments', $output);
		}
	}