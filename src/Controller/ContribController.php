<?php
	namespace App\Controller;

	use App\Contrib\ApiErrors\MissingJournalError;
	use App\Contrib\ContribHelper;
	use App\Contrib\EntityType;
	use DaybreakStudios\DozeBundle\ResponderService;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class ContribController {
		/**
		 * @var ResponderService
		 */
		protected $responder;

		/**
		 * @var ContribHelper
		 */
		protected $helper;

		/**
		 * ContribController constructor.
		 *
		 * @param ResponderService $responder
		 * @param ContribHelper    $helper
		 */
		public function __construct(ResponderService $responder, ContribHelper $helper) {
			$this->responder = $responder;
			$this->helper = $helper;
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

			try {
				$path = $this->helper->getContribPath($type, $id);
			} catch (\JsonException $e) {
				return $this->responder->createErrorResponse(new MissingJournalError($type));
			}

			if (!$path || !file_exists($path))
				return $this->responder->createNotFoundResponse();

			return new JsonResponse(file_get_contents($path), Response::HTTP_OK, [
				'Cache-Control' => 'public, max-age=14400',
				'Content-Type' => 'application/json',
			], true);
		}

		/**
		 * @Route(path="/contrib/{type<a-z-]+>}/{id<\d+>}", methods={"PATCH"}, name="contrib.update")
		 *
		 * @param string $type
		 * @param string $id
		 *
		 * @return Response
		 */
		public function update(string $type, string $id): Response {
			// TODO Add contrib update method body
		}
	}