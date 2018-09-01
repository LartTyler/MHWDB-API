<?php
	namespace App\Controller;

	use App\Entity\MotionValue;
	use App\Game\WeaponType;
	use App\QueryDocument\Projection;
	use App\Response\UnknownWeaponTypeError;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Routing\RouterInterface;

	class MotionValuesDataController extends AbstractDataController {
		/**
		 * MotionValuesDataController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 */
		public function __construct(RegistryInterface $doctrine, ResponderService $responder, RouterInterface $router) {
			parent::__construct($doctrine, $responder, $router, MotionValue::class);
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

			return $this->respond($this->manager->getRepository(MotionValue::class)->findBy([
				'weaponType' => $type,
			]));
		}

		/**
		 * @Route(path="/motion-values/{id<\d+>}", methods={"GET"}, name="motion-values.read")
		 *
		 * @param string $id
		 *
		 * @return Response
		 */
		public function read(string $id): Response {
			return parent::read($id);
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