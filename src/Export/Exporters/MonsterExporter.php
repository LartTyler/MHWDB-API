<?php
	namespace App\Export\Exporters;

	use App\Entity\Monster;
	use App\Entity\MonsterResistance;
	use App\Entity\MonsterWeakness;
	use App\Export\Export;
	use App\Export\ExportHelper;

	class MonsterExporter extends AbstractExporter {
		/**
		 * @var ExportHelper
		 */
		protected $helper;

		/**
		 * MonsterExporter constructor.
		 *
		 * @param ExportHelper $helper
		 */
		public function __construct(ExportHelper $helper) {
			parent::__construct(Monster::class);

			$this->helper = $helper;
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Monster))
				throw new \InvalidArgumentException('$object must be an instance of ' . Monster::class);

			$output = [
				'name' => $object->getName(),
				'type' => $object->getType(),
				'species' => $object->getSpecies(),
				'ailments' => $this->helper->getReferenceArray($object->getAilments(), 'ailments.read'),
				'locations' => $this->helper->getReferenceArray($object->getLocations(), 'locations.read'),
				'resistances' => $object->getResistances()->map(function(MonsterResistance $resistance): array {
					$output = [
						'element' => $resistance->getElement(),
						'condition' => $resistance->getCondition(),
					];

					ksort($output);

					return $output;
				})->toArray(),
				'weaknesses' => $object->getWeaknesses()->map(function(MonsterWeakness $weakness): array {
					$output = [
						'element' => $weakness->getElement(),
						'stars' => $weakness->getStars(),
						'condition' => $weakness->getCondition(),
					];

					ksort($output);

					return $output;
				})->toArray(),
				'description' => $object->getDescription(),
				'elements' => $object->getElements(),
			];

			ksort($output);

			return new Export('monsters', $output);
		}
	}