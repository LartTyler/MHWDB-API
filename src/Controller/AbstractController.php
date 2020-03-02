<?php
	namespace App\Controller;

	use App\Entity\Item;
	use App\Entity\RewardCondition;
	use App\Entity\Strings\ItemStrings;
	use App\Entity\User;
	use App\Localization\L10nUtil;
	use App\Localization\TranslatableEntityInterface;
	use App\Utility\NullObject;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\RestApiCommon\Controller\AbstractApiController;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Collection;
	use Symfony\Component\HttpFoundation\RequestStack;

	abstract class AbstractController extends AbstractApiController {
		/**
		 * @var RequestStack|null
		 */
		protected $requestStack = null;

		/**
		 * @required
		 *
		 * @param RequestStack $requestStack
		 *
		 * @return void
		 */
		public function setRequestStack(RequestStack $requestStack): void {
			$this->requestStack = $requestStack;
		}

		/**
		 * @return User
		 */
		protected function getUser(): User {
			$user = parent::getUser();
			assert($user instanceof User);

			return $user;
		}

		/**
		 * @param TranslatableEntityInterface $entity
		 *
		 * @return NullObject|EntityInterface
		 */
		protected function getStrings(TranslatableEntityInterface $entity): object {
			return NullObject::of(
				L10nUtil::findStrings($this->requestStack->getCurrentRequest()->getLocale(), $entity)
			);
		}

		/**
		 * @param Projection $projection
		 * @param string     $prefix
		 * @param Item       $item
		 *
		 * @return array
		 */
		protected function normalizeItem(Projection $projection, string $prefix, Item $item): array {
			$output = [
				'id' => $item->getId(),
				'rarity' => $item->getRarity(),
				'value' => $item->getValue(),
				'carryLimit' => $item->getCarryLimit(),
				'buyPrice' => $item->getBuyPrice(),
				'sellPrice' => $item->getSellPrice(),
			];

			if ($projection->isAllowed($prefix . '.name') || $projection->isAllowed($prefix . '.description')) {
				/** @var ItemStrings $strings */
				$strings = $this->getStrings($item);

				$output += [
					'name' => $strings->getName(),
					'description' => $strings->getDescription(),
				];
			}

			return $output;
		}

		/**
		 * @param RewardCondition $condition
		 *
		 * @return array
		 */
		protected function normalizeRewardCondition(RewardCondition $condition): array {
			return [
				'type' => $condition->getType(),
				'rank' => $condition->getRank(),
				'quantity' => $condition->getQuantity(),
				'chance' => $condition->getChance(),
				'subtype' => $condition->getSubtype(),
			];
		}
	}