<?php
	namespace App\Contrib\Transformers;

	use App\Entity\Camp;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\Strings\CampStrings;
	use App\Localization\L10nUtil;
	use App\Utility\NullObject;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\EntityTransformerException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;
	use DaybreakStudios\Utility\EntityTransformers\Utility\ObjectUtil;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\HttpFoundation\RequestStack;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	class LocationTransformer extends BaseTransformer {
		/**
		 * @var RequestStack
		 */
		protected $requestStack;

		/**
		 * LocationTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface     $validator
		 * @param RequestStack           $requestStack
		 */
		public function __construct(
			EntityManagerInterface $entityManager,
			ValidatorInterface $validator,
			RequestStack $requestStack
		) {
			parent::__construct($entityManager, $validator);

			$this->requestStack = $requestStack;
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function doCreate(object $data): EntityInterface {
			$missing = ObjectUtil::getMissingProperties(
				$data,
				[
					'name',
					'zoneCount',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new Location($data->name, $data->zoneCount);
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function doUpdate(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Location))
				throw EntityTransformerException::subjectNotSupported($entity);

			if (ObjectUtil::isset($data, 'name'))
				$entity->setName($data->name);

			if (ObjectUtil::isset($data, 'zoneCount'))
				$entity->setZoneCount($data->zoneCount);

			if (ObjectUtil::isset($data, 'camps')) {
				$zones = [];

				foreach ($data->camps as $index => $definition) {
					$missing = ObjectUtil::getMissingProperties(
						$definition,
						[
							'name',
							'zone',
						]
					);

					if ($missing) {
						throw ValidationException::missingFields(
							array_map(
								function(string $key) use ($index): string {
									return 'camps[' . $index . '].' . $key;
								},
								$missing
							)
						);
					} else if ($definition->zone > $entity->getZoneCount()) {
						throw ValidationException::invalidFieldValue(
							'camps[' . $index . '].zone',
							'The location only has ' . $entity->getZoneCount() . ' zone(s)'
						);
					}

					$zones[] = $definition->zone;

					$camp = $entity->getCamp($definition->zone);

					if (!$camp)
						$entity->getCamps()->add($camp = new Camp($entity, $definition->zone));

					$this->getCampStrings($camp)->setName($definition->name);
				}

				if ($zones) {
					$matching = $entity->getCamps()->matching(
						Criteria::create()
							->where(Criteria::expr()->notIn('zone', $zones))
					);

					foreach ($matching as $item)
						$entity->getCamps()->removeElement($item);
				} else
					$entity->getCamps()->clear();
			}
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function doDelete(EntityInterface $entity): void {
			if (!($entity instanceof Location))
				throw EntityTransformerException::subjectNotSupported($entity);

			/** @var Monster[] $monsters */
			$monsters = $this->entityManager->getRepository(Monster::class)->findByLocation($entity);

			foreach ($monsters as $monster)
				$monster->getLocations()->removeElement($entity);
		}

		/**
		 * @param Camp $camp
		 *
		 * @return CampStrings
		 */
		protected function getCampStrings(Camp $camp): CampStrings {
			$strings = L10nUtil::findStringsForTag(
				$lang = $this->requestStack->getCurrentRequest()->getLocale(),
				$camp->getStrings()
			);

			if ($strings instanceof NullObject)
				$camp->getStrings()->add($strings = new CampStrings($camp, $lang));

			return $strings;
		}
	}