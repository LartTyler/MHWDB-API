<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use App\Entity\ArmorSetBonusRank;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\FextralifeConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\Scrapers\Helpers\FextralifeHelper;
	use App\Scraping\Type;
	use App\Utility\StringUtil;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Persistence\ObjectManager;
	use Doctrine\ORM\EntityManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class FextralifeArmorSetBonusScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		private const RANK_REGEXES = [
			/** @lang RegExp */
			'/\\+(\d+) %s/',

			/** @lang RegExp */
			'/%s \\+(\d+)/',
		];

		/**
		 * @var ObjectManager|EntityManager
		 */
		protected $manager;

		/**
		 * @var ArmorSetBonus[]
		 */
		protected $bonusCache = [];

		/**
		 * FextralifeArmorSetBonusScraper constructor.
		 *
		 * @param FextralifeConfiguration $configuration
		 * @param ObjectManager           $manager
		 */
		public function __construct(FextralifeConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::ARMOR_SET_BONUS);

			$this->manager = $manager;
		}

		/**
		 * @param array $context
		 *
		 * @return void
		 */
		public function scrape(array $context = []): void {
			/** @var ArmorSet[] $sets */
			$sets = $this->manager->createQueryBuilder()
				->from('App:ArmorSet', 's')
				->leftJoin('s.pieces', 'p')
				->select('s')
				->addGroupBy('s')
				->having('COUNT(p) > 1')
				->getQuery()
					->getResult();

			$this->progressBar->append(sizeof($sets));

			foreach ($sets as $set) {
				$set->setBonus(null);

				$slug = str_replace(' ', '+', $set->getName()) . '+Armor+Set';

				$uri = $this->configuration->getBaseUri()->withPath('/' . $slug);
				$result = $this->getWithRetry($uri);

				if ($result->getStatusCode() !== Response::HTTP_OK) {
					$newLines = str_repeat(PHP_EOL, 4);

					echo $newLines . '>> [Set Bonus] Could not find page for ' . $set->getName() . $newLines . PHP_EOL;
				}

				$nodes = (new Crawler($result->getBody()->getContents()))->filter('#wiki-content-block ul li');

				for ($i = 0, $ii = $nodes->count(); $i < $ii; $i++) {
					$node = $nodes->eq($i);
					$text = StringUtil::clean($node->text());

					if (preg_match('/^Set notes: /i', $text) === 1) {
						$link = $node->filter('a.wiki_link');

						// If there are no links, there are no bonuses (hopefully)
						if (!$link->count())
							break;

						$bonusName = StringUtil::clean($link->first()->text());

						$this->process($set, $bonusName, $this->extractRankInfo($node));

						break;
					}
				}

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param ArmorSet $set
		 * @param string   $bonusName
		 * @param array    $rankInfo
		 * 	$rankInfo = [
		 * 		[
		 * 			'pieces' => (int) The number of pieces needed to grant the rank
		 * 			'skillName' => (string) The name of the skill that will be granted by the rank
		 * 		]
		 * 	]
		 *
		 * @return void
		 */
		protected function process(ArmorSet $set, string $bonusName, array $rankInfo): void {
			$bonus = $this->getBonus($bonusName);

			if (!$bonus) {
				$bonus = new ArmorSetBonus($bonusName);
				$set->setBonus($bonus);

				$this->manager->persist($this->bonusCache[$bonusName] = $bonus);
			} else
				$set->setBonus($bonus);

			// Stores which rank piece counts were scraped, so we can remove old ones later
			$ranks = [];

			foreach ($rankInfo as $info) {
				$skill = $this->manager->getRepository('App:Skill')->findOneBy([
					'name' => $info['skillName'],
				]);

				if (!$skill)
					throw new \RuntimeException('Could not find skill named ' . $info['skillName']);

				$rank = $bonus->getRank($info['pieces']);

				if (!$rank) {
					$rank = new ArmorSetBonusRank($bonus, $info['pieces'], $skill->getRank(1));
					$bonus->getRanks()->add($rank);
				} else
					$rank->setSkill($skill->getRank(1));

				$ranks[] = $info['pieces'];
			}

			$criteria = Criteria::create()
				->where(Criteria::expr()->notIn('pieces', $ranks));

			$matching = $bonus->getRanks()->matching($criteria);

			foreach ($matching as $match) {
				$bonus->getRanks()->removeElement($match);

				$this->manager->remove($match);
			}
		}

		/**
		 * @param string $name
		 *
		 * @return ArmorSetBonus|null
		 */
		protected function getBonus(string $name): ?ArmorSetBonus {
			if (isset($this->bonusCache[$name]))
				return $this->bonusCache[$name];

			$bonus = $this->manager->getRepository('App:ArmorSetBonus')->findOneBy([
				'name' => $name,
			]);

			return $this->bonusCache[$name] = $bonus;
		}

		/**
		 * @param Crawler $node
		 *
		 * @return array
		 */
		protected function extractRankInfo(Crawler $node): array {
			$links = $node->filter('a.wiki_link');
			$text = StringUtil::clean($node->text());

			$info = [];

			// Skip the first link, since it will always be a link to the set bonus skill
			for ($i = 1, $ii = $links->count(); $i < $ii; $i++) {
				$skillName = FextralifeHelper::fixSkillName(StringUtil::clean($links->eq($i)->text()));

				foreach (self::RANK_REGEXES as $regex) {
					preg_match(sprintf($regex, str_replace('/', '\\/', $skillName)), $text, $matches);

					if ($matches) {
						$info[] = [
							'skillName' => str_replace(' / ', '/', $skillName),
							'pieces' => (int)$matches[1],
						];

						break;
					}
				}
			}

			return $info;
		}
	}