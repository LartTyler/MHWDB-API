<?php
	namespace App\Controller;

	use App\Contrib\Transformers\MotionValueTransformer;
	use App\Entity\MotionValue;
	use App\Game\WeaponType;
	use App\Response\UnknownWeaponTypeError;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class MotionValuesController extends AbstractController {
		/**
		 * MotionValuesDataController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, MotionValue::class);
		}

		/**
		 * @Route(path="/motion-values", methods={"GET"}, name="motion-values.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return $this->doList($request);
		}

		/**
		 * @Route(path="/motion-values/{type<[A-Za-z-]+>}", methods={"GET"}, name="motion-values.list-by-type")
		 *
		 * @param Request $request
		 * @param string  $type
		 *
		 * @return Response
		 */
		public function listByType(Request $request, string $type) {
			$type = strtolower($type);

			if (!WeaponType::isValid($type))
				return $this->respond($request, new UnknownWeaponTypeError($type));

			return $this->respond(
				$request,
				$this->entityManager->getRepository(MotionValue::class)->findBy(
					[
						'weaponType' => $type,
					]
				)
			);
		}

		/**
		 * @Route(path="/motion-values", methods={"PUT"}, name="motion-values.create")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param MotionValueTransformer $transformer
		 * @param Request                $request
		 *
		 * @return Response
		 */
		public function create(MotionValueTransformer $transformer, Request $request): Response {
			return $this->doCreate($transformer, $request);
		}

		/**
		 * @Route(path="/motion-values/{motionValue<\d+>}", methods={"GET"}, name="motion-values.read")
		 *
		 * @param Request     $request
		 * @param MotionValue $motionValue
		 *
		 * @return Response
		 */
		public function read(Request $request, MotionValue $motionValue): Response {
			return $this->respond($request, $motionValue);
		}

		/**
		 * @Route(path="/motion-values/{motionValue<\d+>}", methods={"PATCH"}, name="motion-values.update")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param MotionValueTransformer $transformer
		 * @param Request                $request
		 * @param MotionValue            $motionValue
		 *
		 * @return Response
		 */
		public function update(
			MotionValueTransformer $transformer,
			Request $request,
			MotionValue $motionValue
		): Response {
			return $this->doUpdate($transformer, $motionValue, $request);
		}

		/**
		 * @Route(path="/motion-values/{motionValue<\d+>}", methods={"DELETE"}, name="motion-values.delete")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param MotionValueTransformer $transformer
		 * @param MotionValue            $motionValue
		 *
		 * @return Response
		 */
		public function delete(MotionValueTransformer $transformer, MotionValue $motionValue): Response {
			return $this->doDelete($transformer, $motionValue);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof MotionValue);

			return [
				'id' => $entity->getId(),
				'name' => $entity->getName(),
				'weaponType' => $entity->getWeaponType(),
				'damageType' => $entity->getDamageType(),
				'stun' => $entity->getStun(),
				'exhaust' => $entity->getExhaust(),
				'hits' => $entity->getHits(),
			];
		}
	}