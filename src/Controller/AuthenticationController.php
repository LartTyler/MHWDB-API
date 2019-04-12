<?php
	namespace App\Controller;

	use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class AuthenticationController extends AbstractController {
		/**
		 * @Route(path="/auth/refresh", methods={"GET"}, name="auth.refresh")
		 * @IsGranted("ROLE_USER")
		 *
		 * @param JWTTokenManagerInterface $tokenManager
		 *
		 * @return Response
		 */
		public function refresh(JWTTokenManagerInterface $tokenManager): Response {
			return new JsonResponse(
				[
					'token' => $tokenManager->create($this->getUser()),
				]
			);
		}
	}