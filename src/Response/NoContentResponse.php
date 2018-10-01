<?php
	namespace App\Response;

	use Symfony\Component\HttpFoundation\Response;

	class NoContentResponse extends Response {
		/**
		 * NoContentResponse constructor.
		 *
		 * @param array $headers
		 */
		public function __construct(array $headers = []) {
			parent::__construct('', Response::HTTP_NO_CONTENT, $headers);
		}
	}