<?php
	namespace App\Controller;

	use App\Entity\SluggableInterface;
	use App\Response\SlugNotSupportedError;
	use DaybreakStudios\Doze\Errors\ApiErrorInterface;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\EntityManager;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\RouterInterface;

	abstract class AbstractDataController extends Controller {
		/**
		 * @var EntityManager
		 */
		protected $manager;

		/**
		 * @var ResponderService
		 */
		protected $responder;

		/**
		 * @var RouterInterface
		 */
		protected $router;

		/**
		 * @var string
		 */
		protected $entityClass;

		/**
		 * AbstractCrudController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 * @param string            $entityClass
		 */
		public function __construct(
			RegistryInterface $doctrine,
			ResponderService $responder,
			RouterInterface $router,
			string $entityClass
		) {
			$this->manager = $doctrine->getManager();
			$this->responder = $responder;
			$this->router = $router;
			$this->entityClass = $entityClass;
		}

		/**
		 * @return Response
		 */
		public function listAction(): Response {
			$items = $this->manager->getRepository($this->entityClass)->findAll();

			return $this->responder->createResponse($items);
		}

		/**
		 * @param string $idOrSlug
		 *
		 * @return Response
		 */
		public function readAction(string $idOrSlug): Response {
			return $this->respond($this->getEntity($idOrSlug));
		}

		/**
		 * @param ApiErrorInterface|array|object|null $data
		 *
		 * @return Response
		 */
		protected function respond($data): Response {
			if ($data instanceof ApiErrorInterface)
				return $this->responder->createErrorResponse($data);
			else if ($data === null)
				return $this->responder->createNotFoundResponse();

			return $this->responder->createResponse($data);
		}

		/**
		 * @param string $idOrSlug
		 *
		 * @return SlugNotSupportedError|EntityInterface|null
		 */
		protected function getEntity(string $idOrSlug) {
			if (is_numeric($idOrSlug))
				$item = $this->manager->getRepository($this->entityClass)->find((int)$idOrSlug);
			else {
				if (!is_a($this->entityClass, SluggableInterface::class, true))
					return new SlugNotSupportedError();

				$item = $this->manager->getRepository($this->entityClass)->findOneBy([
					'slug' => $idOrSlug,
				]);
			}

			return $item;
		}
	}