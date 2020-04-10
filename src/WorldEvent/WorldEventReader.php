<?php
	namespace App\WorldEvent;

	use App\Entity\Quest;
	use App\Entity\WorldEvent;
	use App\Game\Expansion;
	use App\Game\PlatformExclusivityType;
	use App\Game\PlatformType;
	use App\Game\WorldEventType;
	use App\Localization\LanguageTag;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\DomCrawler\Crawler;

	class WorldEventReader {
		public const PLATFORM_TYPE_MAP = [
			PlatformType::CONSOLE => [
				Expansion::BASE => 'http://game.capcom.com/world/us/schedule.html',
				Expansion::ICEBORNE => 'http://game.capcom.com/world/us/schedule-master.html',
			],
			PlatformType::PC => [
				Expansion::BASE => 'http://game.capcom.com/world/steam/us/schedule.html',
				Expansion::ICEBORNE => 'http://game.capcom.com/world/steam/us/schedule-master.html',
			],
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
		 * WorldEventReader constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 */
		public function __construct(EntityManagerInterface $entityManager) {
			$this->entityManager = $entityManager;
		}

		/**
		 * @param string $platform
		 * @param string $expansion
		 *
		 * @return \Generator|WorldEvent[]
		 */
		public function read(string $platform, string $expansion): \Generator {
			// Used to infer years for event terms during parsing.
			$currentTimestamp = new \DateTimeImmutable();

			$url = static::PLATFORM_TYPE_MAP[$platform][$expansion] ?? null;

			if (!$url) {
				throw new \InvalidArgumentException(
					sprintf('No URL found for platform and expansion: %s, %s', $platform, $expansion ?? 'NULL')
				);
			}

			$crawler = new Crawler(file_get_contents($url));

			$timezoneOffset = (int)$crawler->filter('label[for=zoneSelect]')->attr('data-zone');
			$offsetInterval = new \DateInterval(
				'PT' . abs($timezoneOffset) . 'H'
			);

			if ($timezoneOffset < 0)
				$offsetInterval->invert = true;

			$tables = $crawler->filter('#schedule .tableArea > table');

			for ($tableIndex = 0, $tablesLength = $tables->count(); $tableIndex < $tablesLength; $tableIndex++) {
				$rows = $tables->eq($tableIndex)->filter('tbody > tr');

				for ($i = 0, $ii = $rows->count(); $i < $ii; $i++) {
					$row = $rows->eq($i);

					$questInfo = $row->filter('td.quest');
					$name = trim($questInfo->filter('.title > span')->text());

					$quest = $this->entityManager->getRepository(Quest::class)
						->findOneByName(LanguageTag::ENGLISH, $name);

					// TODO Need better handling for this, preferably via Discord notifications (or similar)
					if (!$quest)
						throw new \RuntimeException('Missing quest ' . $name);

					$termStrings = preg_split(
						'/(\d{2}-\d{2} \d{2}:\d{2} 〜 \d{2}-\d{2} \d{2}:\d{2})/',
						mb_strtolower($questInfo->filter('.terms')->text()),
						null,
						PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
					);

					/** @var \DateTimeImmutable[][] $terms */
					$terms = [];

					foreach ($termStrings as $item) {
						// Fix for duplicated values in terms node
						if (isset($terms[$item]))
							continue;

						$text = trim($item);

						if (!$text || !is_numeric($text[0]))
							continue;

						$text = str_replace('-', '/', $text);

						$start = (new \DateTime(substr($text, 0, 10), new \DateTimeZone('UTC')))
							->sub($offsetInterval);

						if ($start->diff($currentTimestamp)->d >= 90) {
							$start->setDate(
								(int)$start->format('Y') - 1,
								(int)$start->format('m'),
								(int)$start->format('d')
							);
						}

						$end = (new \DateTime(substr($text, 15), new \DateTimeZone('UTC')))
							->sub($offsetInterval);

						if ($end->diff($currentTimestamp)->days >= 90) {
							$end->setDate(
								(int)$end->format('Y') + 1,
								(int)$end->format('m'),
								(int)$end->format('d')
							);
						}

						$terms[$item] = [
							\DateTimeImmutable::createFromMutable($start),
							\DateTimeImmutable::createFromMutable($end),
						];
					}

					foreach ($terms as $term) {
						$event = $this->entityManager->getRepository(WorldEvent::class)->findOneBy(
							[
								'platform' => $platform,
								'startTimestamp' => $term[0],
								'quest' => $quest,
							]
						);

						if ($event)
							continue;

						$type = static::TABLE_TYPE_MAP[$tables->eq($tableIndex)->attr('class')];

						if ($expansion === Expansion::ICEBORNE && $type === WorldEventType::KULVE_TAROTH)
							$type = WorldEventType::SAFI_JIIVA;

						$event = new WorldEvent(
							$quest,
							$type,
							$expansion,
							$platform,
							$term[0],
							$term[1]
						);

						if ($row->filter('.image > .ps4')->count() > 0)
							$event->setExclusive(PlatformExclusivityType::PS4);

						yield $event;
					}
				}
			}
		}
	}
