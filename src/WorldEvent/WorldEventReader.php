<?php
	namespace App\WorldEvent;

	use App\Entity\Location;
	use App\Entity\WorldEvent;
	use App\Game\PlatformExclusivityType;
	use App\Game\PlatformType;
	use App\Game\WorldEventType;
	use Doctrine\ORM\EntityManagerInterface;
	use Http\Client\Common\HttpMethodsClient;
	use Http\Discovery\HttpClientDiscovery;
	use Http\Discovery\MessageFactoryDiscovery;
	use Symfony\Component\DomCrawler\Crawler;

	class WorldEventReader {
		public const PLATFORM_TYPE_MAP = [
			PlatformType::CONSOLE => 'http://game.capcom.com/world/us/schedule.html',
			PlatformType::PC => 'http://game.capcom.com/world/steam/us/schedule.html',
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
		 *
		 * @return \Generator|WorldEvent[]
		 */
		public function read(string $platform): \Generator {
			$crawler = new Crawler(file_get_contents(static::PLATFORM_TYPE_MAP[$platform]));
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

			$tables = $crawler
				->filter('#schedule .tableArea > table');

			for ($tableIndex = 0, $tablesLength = $tables->count(); $tableIndex < $tablesLength; $tableIndex++) {
				$rows = $tables->eq($tableIndex)->filter('tbody > tr');

				for ($i = 0, $ii = $rows->count(); $i < $ii; $i++) {
					$row = $rows->eq($i);
					$questInfo = $row->filter('td.quest');

					$popupItems = $questInfo->filter('.pop li > span');

					$location = $this->entityManager->getRepository(Location::class)->findOneBy(
						[
							'name' => $locName = trim($popupItems->eq(0)->text()),
						]
					);

					if (!$location)
						throw new \Exception('Unrecognized location: ' . $locName);

					$name = trim($questInfo->filter('.title > span')->text());
					$rank = (int)substr(trim($row->filter('.level')->text()), 0, 1);

					$description = str_replace("\r\n", "\n", trim($questInfo->filter('.txt')->text()));
					$successConditions = trim($popupItems->eq(2)->text());
					$requirements = trim($popupItems->eq(1)->text());

					if (strtolower($requirements) === 'none')
						$requirements = null;

					$exclusive = null;

					if ($row->filter('.image > .ps4')->count() > 0)
						$exclusive = PlatformExclusivityType::PS4;

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
						$event = $this->entityManager->getRepository(WorldEvent::class)->findOneBy(
							[
								'name' => $name,
								'startTimestamp' => $term[0],
								'platform' => $platform,
							]
						);

						if ($event)
							continue;

						$event = new WorldEvent(
							$name,
							static::TABLE_TYPE_MAP[$tables->eq($tableIndex)->attr('class')],
							$platform,
							$term[0],
							$term[1],
							$location,
							$rank
						);

						if ($description)
							$event->setDescription($description);

						if ($requirements)
							$event->setRequirements($requirements);

						if ($successConditions)
							$event->setSuccessConditions($successConditions);

						if ($exclusive)
							$event->setExclusive($exclusive);

						yield $event;
					}
				}
			}
		}
	}
