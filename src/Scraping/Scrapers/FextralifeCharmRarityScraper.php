<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\CharmRank;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\FextralifeConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\Type;
	use App\Utility\RomanNumeral;
	use App\Utility\StringUtil;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class FextralifeCharmRarityScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		/**
		 * @var ObjectManager
		 */
		private $manager;

		/**
		 * FextralifeCharmRarityScraper constructor.
		 *
		 * @param FextralifeConfiguration $configuration
		 * @param ObjectManager           $manager
		 */
		public function __construct(FextralifeConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::CHARM_RARITY);

			$this->manager = $manager;
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(array $context = []): void {
			$charms = $this->manager->getRepository('App:Charm')->findAll();

			$this->progressBar->append(sizeof($charms));

			foreach ($charms as $charm) {
				$baseRarity = 0;

				/** @var CharmRank $rank */
				foreach ($charm->getRanks() as $rank) {
					$slug = strtr(preg_replace_callback('/\d+$/', function(array $matches) {
						return RomanNumeral::toNumeral((int)$matches[0]);
					}, $rank->getName()), ' ', '+');

					$uri = $this->configuration->getBaseUri()->withPath('/' . $slug);
					$response = $this->getWithRetry($uri);

					if ($response->getStatusCode() === Response::HTTP_NOT_FOUND) {
						$this->write('Fextralife does not have ' . $rank->getName());

						continue;
					} else if ($response->getStatusCode() !== Response::HTTP_OK)
						throw new \RuntimeException('Could not retrieve ' . $uri);

					$rarity = (new Crawler($response->getBody()->getContents()))->filter('#infobox .wiki_table tr')
						->eq(2)
						->filter('td')
						->last()
						->text();

					$rarity = StringUtil::clean($rarity);

					if (!is_numeric($rarity)) {
						if ($baseRarity === 0) {
							$this->write(sprintf('Fextralife has "%s" as the rarity for %s, which is not a number',
								$rarity, $rank->getName()));

							continue;
						}

						$rarity = ++$baseRarity;

						$this->write(sprintf('Inferring rarity to be %d for %s', $rarity, $rank->getName()));
					} else if ($baseRarity === 0)
						$rarity = $baseRarity = (int)$rarity;

					$rank->setRarity($rarity);
				}

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}
	}