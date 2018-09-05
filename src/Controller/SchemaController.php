<?php
	namespace App\Controller;

	use DaybreakStudios\DozeBundle\ResponderService;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class SchemaController extends Controller {
		/**
		 * @var ResponderService
		 */
		private $responder;

		/**
		 * SchemaController constructor.
		 *
		 * @param ResponderService $responder
		 */
		public function __construct(ResponderService $responder) {
			$this->responder = $responder;
		}

		/**
		 * @Route(path="/schemas/{schema<[a-z-]+>}", methods={"GET"}, name="schemas.read")
		 *
		 * @param string $schema
		 *
		 * @return Response
		 */
		public function read(string $schema): Response {
			$path = $this->getParameter('kernel.project_dir') . '/src/Resources/schemas/' . $schema . '.schema.json';

			if (!file_exists($path))
				return $this->responder->createNotFoundResponse();

			return new JsonResponse(file_get_contents($path), Response::HTTP_OK, [
				'Cache-Control' => 'public, max-age=14400',
				'Content-Type' => 'application/json',
			], true);
		}
	}