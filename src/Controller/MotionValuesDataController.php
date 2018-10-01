<?php
	namespace App\Controller;

	use App\Contrib\Transformers\MotionValueTransformer;
	use App\Entity\MotionValue;
	use App\Game\WeaponType;
	use App\QueryDocument\Projection;
	use App\Response\UnknownWeaponTypeError;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class MotionValuesDataController extends AbstractDataController {
		/**
		 * MotionValuesDataController constructor.
		 */
		public function __construct() {
			parent::__construct(MotionValue::class);
		}

		/**
		 * @Route(path="/motion-values", methods={"GET"}, name="motion-values.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return parent::list($request);
		}

		/**
		 * @Route(path="/motion-values/{type<[A-Za-z-]+>}", methods={"GET"}, name="motion-values.list-by-type")
		 *
		 * @param string $type
		 *
		 * @return Response
		 */
		public function listByType(string $type) {
			$type = strtolower($type);

			if (!WeaponType::isValid($type))
				return $this->respond(new UnknownWeaponTypeError($type));

			return $this->respond($this->entityManager->getRepository(MotionValue::class)->findBy([
				'weaponType' => $type,
			]));
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
		 * @param MotionValue $motionValue
		 *
		 * @return Response
		 */
		public function read(MotionValue $motionValue): Response {
			return $this->respond($motionValue);
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
		 * @param EntityInterface|MotionValue|null $entity
		 * @param Projection                       $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

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