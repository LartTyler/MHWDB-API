<?php
	namespace App\Controller;

	use App\Contrib\Transformers\AilmentTransformer;
	use App\Entity\Ailment;
	use App\Entity\Item;
	use App\Entity\Skill;
	use App\QueryDocument\Projection;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class AilmentDataController extends AbstractDataController {
		/**
		 * AilmentDataController constructor.
		 */
		public function __construct() {
			parent::__construct(Ailment::class);
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
		 * @Route(path="/ailments", methods={"PUT"}, name="ailments.create")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param AilmentTransformer $transformer
		 * @param Request            $request
		 *
		 * @return Response
		 */
		public function create(AilmentTransformer $transformer, Request $request): Response {
			return $this->doCreate($transformer, $request);
		}

		/**
		 * @Route(path="/ailments/{ailment<\d+>}", methods={"GET"}, name="ailments.read")
		 *
		 * @param Ailment $ailment
		 *
		 * @return Response
		 */
		public function read(Ailment $ailment): Response {
			return $this->respond($ailment);
		}

		/**
		 * @Route(path="/ailments/{ailment<\d+>}", methods={"PATCH"}, name="ailments.update")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param AilmentTransformer $transformer
		 * @param Ailment            $ailment
		 * @param Request            $request
		 *
		 * @return Response
		 */
		public function update(AilmentTransformer $transformer, Ailment $ailment, Request $request): Response {
			return $this->doUpdate($transformer, $ailment, $request);
		}

		/**
		 * @Route(path="/ailments/{ailment<\d+>}", methods={"DELETE"}, name="ailments.delete")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param AilmentTransformer $transformer
		 * @param Ailment            $ailment
		 *
		 * @return Response
		 */
		public function delete(AilmentTransformer $transformer, Ailment $ailment): Response {
			return $this->doDelete($transformer, $ailment);
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