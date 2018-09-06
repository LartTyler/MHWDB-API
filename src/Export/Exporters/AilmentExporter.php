<?php
	namespace App\Export\Exporters;

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

			$recovery = $object->getRecovery();
			$protection = $object->getProtection();

			$output = [
				'name' => $object->getName(),
				'description' => $object->getDescription(),
				'recovery' => [
					'actions' => $recovery->getActions(),
					'items' => $this->helper->getReferenceArray($recovery->getItems(), 'items.read'),
				],
				'protection' => [
					'items' => $this->helper->getReferenceArray($protection->getItems(), 'items.read'),
					'skills' => $this->helper->getReferenceArray($protection->getSkills(), 'skills.read', 'idOrSlug'),
				],
			];

			ksort($output);

			return new Export('ailments', $output);
		}
	}