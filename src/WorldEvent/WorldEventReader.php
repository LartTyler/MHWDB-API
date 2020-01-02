<?php
	namespace App\WorldEvent;

	use App\Entity\Location;
	use App\Entity\WorldEvent;
	use App\Game\Expansion;
	use App\Game\PlatformExclusivityType;
	use App\Game\PlatformType;
	use App\Game\WorldEventType;
	use App\Localization\LanguageTag;
	use Doctrine\ORM\EntityManagerInterface;
	use Http\Client\Common\HttpMethodsClient;
	use Http\Discovery\HttpClientDiscovery;
	use Http\Discovery\MessageFactoryDiscovery;
	use Symfony\Component\DomCrawler\Crawler;

	class WorldEventReader {
		public const PLATFORM_TYPE_MAP = [
			PlatformType::CONSOLE => [
				Expansion::BASE => 'http://game.capcom.com/world/%s/schedule.html',
				Expansion::ICEBORNE => 'http://game.capcom.com/world/%s/schedule-master.html',
			],
			PlatformType::PC => [
				Expansion::BASE => 'http://game.capcom.com/world/steam/%s/schedule.html',
			],
		];

		public const LANGUAGE_TAG_MAP = [
			LanguageTag::ENGLISH => 'us',
			LanguageTag::FRENCH => 'fr',
			LanguageTag::GERMAN => 'de',
			LanguageTag::CHINESE_SIMPLIFIED => 'cn',
			LanguageTag::CHINESE_TRADITIONAL => 'hk',
		];

		public const TABLE_TYPE_MAP = [
			'table1' => WorldEventType::KULVE_TAROTH,
			'table2' => WorldEventType::EVENT_QUEST,
			'table3' => WorldEventType::CHALLENGE_QUEST,
		];

		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var HttpMethodsClient
		 */
		protected $client;

		/**
		 * WorldEventReader constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 */
		public function __construct(EntityManagerInterface $entityManager) {
			$this->entityManager = $entityManager;
			$this->client = new HttpMethodsClient(
				HttpClientDiscovery::find(),
				MessageFactoryDiscovery::find()
			);
		}

		/**
		 * @param string $platform
		 * @param string $expansion
		 * @param int    $sleepDuration
		 *
		 * @return \Generator|WorldEvent[]
		 */
		public function read(string $platform, string $expansion, int $sleepDuration = 5): \Generator {
			/**
			 * Holds new events, in the order they are parsed out of the initial page. Events already in the database
			 * are represented by `null` values, to indicate that they should be skipped.
			 *
			 * @var WorldEvent[] $events
			 */
			$events = [];

			/**
			 * {@see LanguageTag::ENGLISH} is always loaded first in order to give us a stable event name to match
			 * against, so that languages added in future releases don't mess with event parsing.
			 */
			$languages = array_merge(
				[LanguageTag::ENGLISH],
				array_filter(
					LanguageTag::values(),
					function(string $language) {
						return $language !== LanguageTag::ENGLISH;
					}
				)
			);

			foreach ($languages as $languageIndex => $language) {
				$url = static::PLATFORM_TYPE_MAP[$platform][$expansion] ?? null;

				if (!$url) {
					throw new \InvalidArgumentException(
						sprintf('No URL found for platform and expansion: %s, %s', $platform, $expansion ?? 'NULL')
					);
				}

				$crawler = new Crawler(file_get_contents($url, static::LANGUAGE_TAG_MAP[$language]));
				$timezoneOffsetNode = $crawler->filter('label[for=zoneSelect]');

				// Pre-Iceborne events page contained a typo in the `for` attribute of the timezone selector. Until the PC
				// event page is updated to the Iceborne layout, we'll need to fall back on the old `for` value.
				if ($timezoneOffsetNode->count() === 0)
					$timezoneOffsetNode = $crawler->filter('label[for=zoonSelect]');

				$timezoneOffset = (int)$timezoneOffsetNode->attr('data-zone');

				$offsetInterval = new \DateInterval(
					'PT' . abs($timezoneOffset) . 'H'
				);

				if ($timezoneOffset < 0)
					$offsetInterval->invert = true;

				$tables = $crawler->filter('#schedule .tableArea > table');
				$eventIndex = 0;

				for ($tableIndex = 0, $tablesLength = $tables->count(); $tableIndex < $tablesLength; $tableIndex++) {
					$rows = $tables->eq($tableIndex)->filter('tbody > tr');

					for ($i = 0, $ii = $rows->count(); $i < $ii; $i++) {
						$row = $rows->eq($i);

						$questInfo = $row->filter('td.quest');
						$popupItems = $questInfo->filter('.pop li > span');

						$name = trim($questInfo->filter('.title > span')->text());

						$termText = trim(
							str_replace(
								[
									'availability',
									'-',
								],
								[
									'',
									'/',
								],
								mb_strtolower($questInfo->filter('.terms')->text())
							)
						);

						/** @var \DateTimeImmutable[][] $terms */
						$terms = [];

						foreach (explode("\n", $termText) as $item) {
							$text = trim($item);

							$start = (new \DateTimeImmutable(substr($text, 0, 10), new \DateTimeZone('UTC')))
								->sub($offsetInterval);

							$end = (new \DateTimeImmutable(substr($text, 15), new \DateTimeZone('UTC')))
								->sub($offsetInterval);

							if ((int)$end->format('m') < (int)$start->format('m')) {
								$end = $end->setDate(
									(int)$end->format('Y') + 1,
									(int)$end->format('m'),
									(int)$end->format('d')
								);
							}

							$terms[] = [$start, $end];
						}

						foreach ($terms as $term) {
							if (!isset($events[$eventIndex])) {
								$event = $this->entityManager->getRepository(WorldEvent::class)->search(
									$language,
									$name,
									$platform,
									$term[0]
								);

								if ($event) {
									$events[$eventIndex++] = null;

									continue;
								}

								/** @var Location|null $location */
								$location = $this->entityManager->getRepository(Location::class)->findOneBy(
									[
										'name' => $locName = trim($popupItems->eq(0)->text()),
									]
								);

								if (!$location)
									throw new \Exception('Unrecognized location: ' . $locName);

								$rank = (int)preg_replace('/\D/', '', $row->filter('.level')->text());
								$exclusive = null;

								if ($row->filter('.image > .ps4')->count() > 0)
									$exclusive = PlatformExclusivityType::PS4;

								$type = static::TABLE_TYPE_MAP[$tables->eq($tableIndex)->attr('class')];

								if ($expansion === Expansion::ICEBORNE && $type === WorldEventType::KULVE_TAROTH)
									$type = WorldEventType::SAFI_JIIVA;

								$events[$eventIndex] = $event = new WorldEvent(
									$type,
									$expansion,
									$platform,
									$term[0],
									$term[1],
									$location,
									$rank
								);

								$event->setMasterRank($expansion === Expansion::ICEBORNE);

								if ($exclusive)
									$event->setExclusive($exclusive);
							} else if ($events[$eventIndex] === null)
								continue;

							$description = str_replace("\r\n", "\n", trim($questInfo->filter('.txt')->text()));
							$successConditions = trim($popupItems->eq(2)->text());
							$requirements = trim($popupItems->eq(1)->text());

							if (strtolower($requirements) === 'none')
								$requirements = null;

							$event = $events[$eventIndex++];
							$strings = $event->addStrings($language);

							$strings->setName($name);

							if ($description)
								$strings->setDescription($description);

							if ($successConditions)
								$strings->setSuccessConditions($successConditions);

							if ($requirements)
								$strings->setRequirements($requirements);

							yield $event;
						}
					}
				}

				if ($sleepDuration > 0 && $languageIndex + 1 < sizeof($languages))
					sleep($sleepDuration);
			}
		}
	}
