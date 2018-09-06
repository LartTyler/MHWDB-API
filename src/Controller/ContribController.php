<?php
	namespace App\Controller;

	use App\Contrib\ApiErrors\MissingJournalError;
	use App\Contrib\EntityType;
	use DaybreakStudios\DozeBundle\ResponderService;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class ContribController {
		/**
		 * @var ResponderService
		 */
		protected $responder;

		/**
		 * @var string
		 */
		protected $contribDir;

		/**
		 * ContribController constructor.
		 *
		 * @param ResponderService $responder
		 * @param string           $contribDir
		 */
		public function __construct(ResponderService $responder, string $contribDir) {
			$this->responder = $responder;
			$this->contribDir = $contribDir;
		}

		/**
		 * @Route(path="/contrib/{type<[a-z-]+>}/{id<\d+>}", methods={"GET"}, name="contrib.read")
		 *
		 * @param string $type
		 * @param string $id
		 *
		 * @return Response
		 */
		public function read(string $type, string $id): Response {
			if (!EntityType::isValid($type))
				return $this->responder->createNotFoundResponse();

			$basePath = $this->contribDir . '/json/' . $type;

			if (!file_exists($basePath))
				return $this->responder->createNotFoundResponse();

			$journal = @json_decode(file_get_contents($basePath . '/.journal.json'), true);

			if (json_last_error() !== JSON_ERROR_NONE)
				return $this->responder->createErrorResponse(new MissingJournalError($type));

			return new JsonResponse(file_get_contents($basePath . '/' . $journal[$id]), Response::HTTP_OK, [
				'Cache-Control' => 'public, max-age=14400',
				'Content-Type' => 'application/json',
			], true);
		}
	}