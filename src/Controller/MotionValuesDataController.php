<?php
	namespace App\Controller;

	use App\Entity\MotionValue;
	use App\Game\WeaponType;
	use App\Response\UnknownWeaponTypeError;
	use DaybreakStudios\Doze\Errors\ApiError;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\HttpFoundation\Response;
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
		 * @param string $type
		 *
		 * @return Response
		 */
		public function listByTypeAction(string $type): Response {
			if (!WeaponType::isValid($type))
				return $this->respond(new UnknownWeaponTypeError($type));

			return $this->respond($this->manager->getRepository('App:MotionValue')->findBy([
				'weaponType' => $type,
			]));
		}

		/**
		 * @param EntityInterface|MotionValue|null $entity
		 *
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