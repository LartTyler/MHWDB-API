<?php
	namespace App\Controller;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Contrib\Management\Entity\AilmentDataManager;
	use App\Entity\Ailment;
	use App\Entity\Item;
	use App\Entity\Skill;
	use App\Import\ImportManager;
	use App\QueryDocument\Projection;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Routing\RouterInterface;

	class AilmentDataController extends AbstractDataController {
		/**
		 * AilmentDataController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 */
		public function __construct(RegistryInterface $doctrine, ResponderService $responder, RouterInterface $router) {
			parent::__construct($doctrine, $responder, $router, Ailment::class, EntityType::AILMENTS);
		}

		/**
		 * @Route(path="/ailments", methods={"GET"}, name="ailments.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return parent::list($request);
		}

		/**
		 * @Route(path="/ailments/{id<\d+>}", methods={"GET"}, name="ailments.read")
		 *
		 * @param string $id
		 *
		 * @return Response
		 */
		public function read(string $id): Response {
			return parent::read($id);
		}

		/**
		 * @Route(path="/ailments/{id<\d+>}", methods={"PATCH"}, name="ailments.update")
		 * @IsGranted("ROLE_USER")
		 *
		 * @param AilmentDataManager $dataManager
		 * @param Request            $request
		 * @param string             $id
		 *
		 * @return Response
		 */
		public function update(
			AilmentDataManager $dataManager,
			Request $request,
			string $id
		): Response {
			return parent::doUpdate($dataManager, $request, $id);
		}

		/**
		 * @param Ailment|EntityInterface|null $entity
		 * @param Projection                   $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

			$output = [
				'id' => $entity->getId(),
				'name' => $entity->getName(),
				'description' => $entity->getDescription(),
			];

			if ($projection->isAllowed('recovery')) {
				$recovery = $entity->getRecovery();

				$output['recovery'] = [
					'actions' => $recovery->getActions(),
				];

				if ($projection->isAllowed('recovery.items')) {
					$output['recovery']['items'] = array_map(
						function(Item $item): array {
							return [
								'id' => $item->getId(),
								'name' => $item->getName(),
								'description' => $item->getDescription(),
								'rarity' => $item->getRarity(),
								'value' => $item->getValue(),
								'carryLimit' => $item->getCarryLimit(),
							];
						},
						$recovery->getItems()->toArray()
					);
				}
			}

			if ($projection->isAllowed('protection')) {
				$protection = $entity->getProtection();

				$output['protection'] = [];

				if ($projection->isAllowed('protection.items')) {
					$output['protection']['items'] = array_map(
						function(Item $item): array {
							return [
								'id' => $item->getId(),
								'name' => $item->getName(),
								'description' => $item->getDescription(),
								'rarity' => $item->getRarity(),
								'value' => $item->getValue(),
								'carryLimit' => $item->getCarryLimit(),
							];
						},
						$protection->getItems()->toArray()
					);
				}

				if ($projection->isAllowed('protection.skills')) {
					$output['protection']['skills'] = array_map(
						function(Skill $skill): array {
							return [
								'id' => $skill->getId(),
								'name' => $skill->getName(),
								'description' => $skill->getDescription(),
							];
						},
						$protection->getSkills()->toArray()
					);
				}

				if (!$output['protection'])
					unset($output['protection']);
			}

			return $output;
		}
	}