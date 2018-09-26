<?php
	namespace App\Controller;

	use App\Entity\MotionValue;
	use App\Game\WeaponType;
	use App\QueryDocument\Projection;
	use App\Response\UnknownWeaponTypeError;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
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