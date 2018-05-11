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
		 * @param EntityInterface|MotionValue|null $motion
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $motion): ?array {
			if (!$motion)
				return null;

			return [
				'id' => $motion->getId(),
				'name' => $motion->getName(),
				'weaponType' => $motion->getWeaponType(),
				'damageType' => $motion->getDamageType(),
				'stun' => $motion->getStun(),
				'exhaust' => $motion->getExhaust(),
				'values' => $motion->getHits(),
			];
		}
	}