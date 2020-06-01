<?php
	namespace App\Controller;

	use App\Entity\Ailment;
	use App\Entity\Camp;
	use App\Entity\EndemicLife;
	use App\Entity\Item;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\MonsterQuestTarget;
	use App\Entity\MonsterResistance;
	use App\Entity\MonsterReward;
	use App\Entity\MonsterWeakness;
	use App\Entity\Quest;
	use App\Entity\QuestReward;
	use App\Entity\RewardCondition;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use App\Entity\Strings\AilmentStrings;
	use App\Entity\Strings\CampStrings;
	use App\Entity\Strings\EndemicLifeStrings;
	use App\Entity\Strings\ItemStrings;
	use App\Entity\Strings\LocationStrings;
	use App\Entity\Strings\MonsterStrings;
	use App\Entity\Strings\MonsterWeaknessStrings;
	use App\Entity\Strings\QuestStrings;
	use App\Entity\Strings\SkillRankStrings;
	use App\Entity\Strings\SkillStrings;
	use App\Entity\User;
	use App\Entity\WorldEvent;
	use App\Game\Quest\DeliveryType;
	use App\Game\Quest\QuestObjective;
	use App\Localization\L10nUtil;
	use App\Localization\TranslatableEntityInterface;
	use App\Utility\NullObject;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\RestApiCommon\Controller\AbstractApiController;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
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
		 * @param Ailment    $ailment
		 *
		 * @return array
		 */
		protected function normalizeAilment(Projection $projection, string $prefix, Ailment $ailment): array {
			$output = [
				'id' => $ailment->getId(),
			];

			if ($this->isAnyAllowed($projection, $prefix, 'name', 'description')) {
				/** @var AilmentStrings $strings */
				$strings = $this->getStrings($ailment);

				$output += [
					'name' => $strings->getName(),
					'description' => $strings->getDescription(),
				];
			}

			if ($this->isAllowed($projection, $prefix, 'recovery')) {
				$recovery = $ailment->getRecovery();

				$output['recovery'] = [
					'actions' => $recovery->getActions(),
				];

				if ($this->isAllowed($projection, $prefix, 'recovery.items')) {
					$output['recovery']['items'] = $recovery->getItems()->map(
						function(Item $item) use ($projection, $prefix) {
							return $this->normalizeItem(
								$projection,
								$this->mergePaths($prefix, 'recovery.items'),
								$item
							);
						}
					);
				}
			}

			if ($this->isAllowed($projection, $prefix, 'protection')) {
				$protection = [];

				if ($this->isAllowed($projection, $prefix, 'protection.skills')) {
					$protection['skills'] = $ailment->getProtection()->getSkills()->map(
						function(Skill $skill) use ($projection, $prefix) {
							return $this->normalizeSkill(
								$projection,
								$this->mergePaths($prefix, 'protection.skills'),
								$skill
							);
						}
					)->toArray();
				}

				if ($this->isAllowed($projection, $prefix, 'protection.items')) {
					$protection['items'] = $ailment->getProtection()->getItems()->map(
						function(Item $item) use ($projection, $prefix) {
							return $this->normalizeItem(
								$projection,
								$this->mergePaths($prefix, 'protection.items'),
								$item
							);
						}
					)->toArray();
				}
			}

			return $output;
		}

		/**
		 * @param Projection $projection
		 * @param string     $prefix
		 * @param Skill      $skill
		 *
		 * @return array
		 */
		protected function normalizeSkill(Projection $projection, string $prefix, Skill $skill): array {
			$output = [
				'id' => $skill->getId(),
			];

			if ($this->isAnyAllowed($projection, $prefix, 'name', 'description')) {
				/** @var SkillStrings $skillStrings */
				$skillStrings = $this->getStrings($skill);

				$output += [
					'name' => $skillStrings->getName(),
					'description' => $skillStrings->getDescription(),
				];
			} else
				$skillStrings = null; // ensure always defined so we can `use` it later

			if ($this->isAllowed($projection, $prefix, 'ranks')) {
				$output['ranks'] = $skill->getRanks()->map(
					function(SkillRank $rank) use ($projection, $prefix, $skill, $skillStrings) {
						$output = [
							'level' => $rank->getLevel(),
							'modifiers' => $rank->getModifiers() ?: new \stdClass(),
						];

						if ($this->isAllowed($projection, $prefix, 'ranks.skillName'))
							$output['skillName'] = ($skillStrings ?? $this->getStrings($skill))->getName();

						if ($this->isAllowed($projection, $prefix, 'ranks.description')) {
							/** @var SkillRankStrings $strings */
							$strings = $this->getStrings($rank);

							$output['description'] = $strings->getDescription();
						}
					}
				)->toArray();
			}

			return $output;
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

			if ($this->isAnyAllowed($projection, $prefix, 'name', 'description')) {
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

		/**
		 * @param Projection $projection
		 * @param string     $prefix
		 * @param WorldEvent $event
		 *
		 * @return array
		 */
		protected function normalizeWorldEvent(Projection $projection, string $prefix, WorldEvent $event): array {
			return [
				'id' => $event->getId(),
				'platform' => $event->getPlatform(),
				'exclusive' => $event->getExclusive(),
				'type' => $event->getType(),
				'expansion' => $event->getExpansion(),
				'startTimestamp' => $event->getStartTimestamp()->format(\DateTime::ISO8601),
				'endTimestamp' => $event->getEndTimestamp()->format(\DateTime::ISO8601),
			];
		}

		/**
		 * @param Projection $projection
		 * @param string     $prefix
		 * @param Quest      $quest
		 *
		 * @return array
		 */
		protected function normalizeQuest(Projection $projection, string $prefix, Quest $quest): array {
			$output = [
				'id' => $quest->getId(),
				'objective' => $quest->getObjective(),
				'type' => $quest->getType(),
				'rank' => $quest->getRank(),
				'stars' => $quest->getStars(),
				'timeLimit' => $quest->getTimeLimit(),
				'maxHunters' => $quest->getMaxHunters(),
				'maxFaints' => $quest->getMaxFaints(),
			];

			if ($this->isAnyAllowed($projection, $prefix, 'name', 'description')) {
				/** @var QuestStrings $strings */
				$strings = $this->getStrings($quest);

				$output += [
					'name' => $strings->getName(),
					'description' => $strings->getDescription(),
				];
			}

			if ($this->isAllowed($projection, $prefix, 'rewards')) {
				$output['rewards'] = $quest->getRewards()->map(
					function(QuestReward $reward) use ($projection, $prefix) {
						$output = [];

						if ($this->isAllowed($projection, $prefix, 'rewards.item')) {
							$output['item'] = $this->normalizeItem(
								$projection,
								$this->mergePaths($prefix, 'rewards.item'),
								$reward->getItem()
							);
						}

						if ($this->isAllowed($projection, $prefix, 'rewards.conditions')) {
							$output['conditions'] = $reward->getConditions()->map(
								function(RewardCondition $condition) use ($projection, $prefix) {
									return $this->normalizeRewardCondition($condition);
								}
							)->toArray();
						}

						return $output;
					}
				)->toArray();
			}

			switch ($quest->getObjective()) {
				case QuestObjective::GATHER:
					$output['amount'] = $quest->getAmount();

					if ($this->isAllowed($projection, $prefix, 'item')) {
						$output['item'] = $this->normalizeItem(
							$projection,
							$this->mergePaths($prefix, 'item'),
							$quest->getItem()
						);
					}

					break;

				case QuestObjective::DELIVER:
					$output += [
						'deliveryType' => $quest->getDeliveryType(),
						'amount' => $quest->getAmount(),
					];

					if (
						$quest->getDeliveryType() === DeliveryType::ENDEMIC_LIFE &&
						$this->isAllowed($projection, $prefix, 'endemicLife')
					) {
						$output['endemicLife'] = $this->normalizeEndemicLife(
							$projection,
							$this->mergePaths($prefix, 'endemicLife'),
							$quest->getEndemicLife()
						);
					} else if (
						$quest->getDeliveryType() === DeliveryType::OBJECT &&
						$this->isAllowed($projection, $prefix, 'objectName')
					)
						$output['objectName'] = ($strings ?? $this->getStrings($quest))->getObjectName();

					break;

				case QuestObjective::CAPTURE:
				case QuestObjective::HUNT:
				case QuestObjective::SLAY:
					if ($this->isAllowed($projection, $prefix, 'targets')) {
						$output['targets'] = $quest->getTargets()->map(
							function(MonsterQuestTarget $target) use ($projection, $prefix) {
								$output = [
									'amount' => $target->getAmount(),
								];

								if ($this->isAllowed($projection, $prefix, 'targets.monster')) {
									$output['monster'] = $this->normalizeMonster(
										$projection,
										$this->mergePaths($prefix, 'targets.monster'),
										$target->getMonster()
									);
								}

								return $output;
							}
						)->toArray();
					}

					break;
			}

			return $output;
		}

		/**
		 * @param Projection $projection
		 * @param string     $prefix
		 * @param Monster    $monster
		 *
		 * @return array
		 */
		protected function normalizeMonster(Projection $projection, string $prefix, Monster $monster): array {
			$output = [
				'id' => $monster->getId(),
				'type' => $monster->getType(),
				'species' => $monster->getSpecies(),
				'elements' => $monster->getElements(),
			];

			if ($this->isAnyAllowed($projection, $prefix, 'name', 'description')) {
				/** @var MonsterStrings $strings */
				$strings = $this->getStrings($monster);

				$output += [
					'name' => $strings->getName(),
					'description' => $strings->getDescription(),
				];
			}

			if ($this->isAllowed($projection, $prefix, 'ailments')) {
				$output['ailments'] = $monster->getAilments()->map(
					function(Ailment $ailment) use ($projection, $prefix) {
						return $this->normalizeAilment(
							$projection,
							$this->mergePaths($prefix, 'ailments'),
							$ailment
						);
					}
				)->toArray();
			}

			if ($this->isAllowed($projection, $prefix, 'locations')) {
				$output['locations'] = $monster->getLocations()->map(
					function(Location $location) use ($projection, $prefix) {
						return $this->normalizeLocation(
							$projection,
							$this->mergePaths($prefix, 'locations'),
							$location
						);
					}
				)->toArray();
			}

			if ($this->isAllowed($projection, $prefix, 'resistances')) {
				$output['resistances'] = $monster->getResistances()->map(
					function(MonsterResistance $resistance) {
						return [
							'element' => $resistance->getElement(),
							'condition' => $resistance->getCondition(),
						];
					}
				)->toArray();
			}

			if ($this->isAllowed($projection, $prefix, 'weaknesses')) {
				$output['weaknesses'] = $monster->getWeaknesses()->map(
					function(MonsterWeakness $weakness) use ($projection, $prefix) {
						$output = [
							'element' => $weakness->getElement(),
							'stars' => $weakness->getStars(),
						];

						if ($this->isAllowed($projection, $prefix, 'weaknesses.condition')) {
							/** @var MonsterWeaknessStrings $strings */
							$strings = $this->getStrings($weakness);

							$output['condition'] = $strings->getCondition();
						}

						return $output;
					}
				)->toArray();
			}

			if ($this->isAllowed($projection, $prefix, 'rewards')) {
				$monster['rewards'] = $monster->getRewards()->map(
					function(MonsterReward $reward) use ($projection, $prefix) {
						$output = [];

						if ($this->isAllowed($projection, $prefix, 'rewards.item')) {
							$output['item'] = $this->normalizeItem(
								$projection,
								$this->mergePaths($prefix, 'rewards.item'),
								$reward->getItem()
							);
						}

						if ($this->isAllowed($projection, $prefix, 'rewards.conditions')) {
							$output['conditions'] = $reward->getConditions()->map(
								function(RewardCondition $condition) use ($projection, $prefix) {
									return $this->normalizeRewardCondition($condition);
								}
							)->toArray();
						}
					}
				)->toArray();
			}

			return $output;
		}

		/**
		 * @param Projection  $projection
		 * @param string      $prefix
		 * @param EndemicLife $endemicLife
		 *
		 * @return array
		 */
		protected function normalizeEndemicLife(
			Projection $projection,
			string $prefix,
			EndemicLife $endemicLife
		): array {
			$output = [
				'id' => $endemicLife->getId(),
				'type' => $endemicLife->getType(),
				'researchPointValue' => $endemicLife->getResearchPointValue(),
				'spawnConditions' => $endemicLife->getSpawnConditions(),
			];

			if ($this->isAnyAllowed($projection, $prefix, 'name', 'description')) {
				/** @var EndemicLifeStrings $strings */
				$strings = $this->getStrings($endemicLife);

				$output += [
					'name' => $strings->getName(),
					'description' => $strings->getDescription(),
				];
			}

			if ($this->isAllowed($projection, $prefix, 'locations')) {
				$output['locations'] = $endemicLife->getLocations()
					->map(
						function(Location $location) use ($projection, $prefix) {
							return $this->normalizeLocation(
								$projection,
								$this->mergePaths($prefix, 'locations'),
								$location
							);
						}
					)
					->toArray();
			}

			return $output;
		}

		/**
		 * @param Projection $projection
		 * @param string     $prefix
		 * @param Location   $location
		 *
		 * @return array
		 */
		protected function normalizeLocation(Projection $projection, string $prefix, Location $location): array {
			$output = [
				'id' => $location->getId(),
				'zoneCount' => $location->getZoneCount(),
			];

			if ($this->isAllowed($projection, $prefix, 'name')) {
				/** @var LocationStrings $strings */
				$strings = $this->getStrings($location);

				$output['name'] = $strings->getName();
			}

			if ($this->isAllowed($projection, $prefix, 'camps')) {
				$output['camps'] = $location->getCamps()->map(
					function(Camp $camp) use ($projection, $prefix) {
						$output = [
							'zone' => $camp->getZone(),
						];

						if ($this->isAllowed($projection, $prefix, 'camps.name')) {
							/** @var CampStrings $strings */
							$strings = $this->getStrings($camp);

							$output['name'] = $strings->getName();
						}

						return $output;
					}
				)->toArray();
			}

			return $output;
		}

		/**
		 * @param Projection $projection
		 * @param string     $prefix
		 * @param string     $path
		 *
		 * @return bool
		 */
		protected function isAllowed(Projection $projection, string $prefix, string $path): bool {
			return $projection->isAllowed($this->mergePaths($prefix, $path));
		}

		/**
		 * @param Projection $projection
		 * @param string     $prefix
		 * @param string[]   $paths
		 *
		 * @return bool
		 */
		protected function isAnyAllowed(Projection $projection, string $prefix, string ...$paths): bool {
			foreach ($paths as $path) {
				if ($this->isAllowed($projection, $prefix, $path))
					return true;
			}

			return false;
		}

		/**
		 * @param string $prefix
		 * @param string $path
		 *
		 * @return string
		 */
		protected function mergePaths(string $prefix, string $path): string {
			return $prefix . ($prefix ? '.' : '') . $path;
		}
	}