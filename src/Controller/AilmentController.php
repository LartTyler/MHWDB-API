<?php
	namespace App\Controller;

	use App\Contrib\Transformers\AilmentTransformer;
	use App\Entity\Ailment;
	use App\Entity\Item;
	use App\Entity\Skill;
	use App\Entity\Strings\AilmentStrings;
	use App\Entity\Strings\ItemStrings;
	use App\Entity\Strings\SkillStrings;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class AilmentController extends AbstractController {
		/**
		 * AilmentDataController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, Ailment::class);
		}

		/**
		 * @Route(path="/ailments", methods={"GET"}, name="ailments.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return $this->doList($request);
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
		 * @param Request $request
		 * @param Ailment $ailment
		 *
		 * @return Response
		 */
		public function read(Request $request, Ailment $ailment): Response {
			return $this->respond($request, $ailment);
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
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof Ailment);

			$output = [
				'id' => $entity->getId(),
			];

			if ($projection->isAllowed('name') || $projection->isAllowed('description')) {
				/** @var AilmentStrings $strings */
				$strings = $this->getStrings($entity);

				$output += [
					'name' => $strings->getName(),
					'description' => $strings->getDescription(),
				];
			}

			if ($projection->isAllowed('recovery')) {
				$recovery = $entity->getRecovery();

				$output['recovery'] = [
					'actions' => $recovery->getActions(),
				];

				if ($projection->isAllowed('recovery.items')) {
					$output['recovery']['items'] = array_map(
						function(Item $item) use ($projection): array {
							$output = [
								'id' => $item->getId(),
								'rarity' => $item->getRarity(),
								'value' => $item->getValue(),
								'carryLimit' => $item->getCarryLimit(),
								'buyPrice' => $item->getBuyPrice(),
								'sellPrice' => $item->getSellPrice(),
							];

							if (
								$projection->isAllowed('recovery.items.name') ||
								$projection->isAllowed('recovery.items.description')
							) {
								/** @var ItemStrings $strings */
								$strings = $this->getStrings($item);

								$output += [
									'name' => $strings->getName(),
									'description' => $strings->getDescription(),
								];
							}

							return $output;
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
						function(Item $item) use ($projection): array {
							$output = [
								'id' => $item->getId(),
								'rarity' => $item->getRarity(),
								'value' => $item->getValue(),
								'carryLimit' => $item->getCarryLimit(),
							];

							if (
								$projection->isAllowed('protection.items.name') ||
								$projection->isAllowed('protection.items.description')
							) {
								/** @var ItemStrings $strings */
								$strings = $this->getStrings($item);

								$output += [
									'name' => $strings->getName(),
									'description' => $strings->getDescription(),
								];
							}

							return $output;
						},
						$protection->getItems()->toArray()
					);
				}

				if ($projection->isAllowed('protection.skills')) {
					$output['protection']['skills'] = array_map(
						function(Skill $skill) use ($projection): array {
							$output = [
								'id' => $skill->getId(),
							];

							if (
								$projection->isAllowed('protection.skills.name') ||
								$projection->isAllowed('protection.skills.description')
							) {
								/** @var SkillStrings $strings */
								$strings = $this->getStrings($skill);

								$output += [
									'name' => $strings->getName(),
									'description' => $strings->getDescription(),
								];
							}

							return $output;
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