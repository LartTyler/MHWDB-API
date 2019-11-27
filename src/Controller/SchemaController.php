<?php
	namespace App\Controller;

	use DaybreakStudios\RestApiCommon\Error\Errors\NotFoundError;
	use DaybreakStudios\RestApiCommon\ResponderService;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class SchemaController extends AbstractController {
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
				return $this->responder->createErrorResponse(new NotFoundError());

			$schema = file_get_contents($path);

			if ($this->getParameter('kernel.environment') === 'dev')
				$schema = str_replace('https://mhw-db.com/schemas/', 'http://localhost:8000/schemas/', $schema);

			return new JsonResponse(
				$schema,
				Response::HTTP_OK,
				[
					'Cache-Control' => 'public, max-age=14400',
					'Content-Type' => 'application/json',
				], true
			);
		}
	}