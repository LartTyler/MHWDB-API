<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Armor;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\GithubConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\Scrapers\Helpers\CsvReader;
	use App\Scraping\Type;
	use Doctrine\Common\Persistence\ObjectManager;
	use Psr\Http\Message\UriInterface;
	use Symfony\Component\HttpFoundation\Response;

	class CarlosFdezMHWorldDataDefenseValuesScraper extends AbstractCarlosFdezMHWorldDataScraper {
		use ProgressAwareTrait;

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * CarlosFdezMHWorldDataDefenseValuesScraper constructor.
		 *
		 * @param GithubConfiguration $configuration
		 * @param ObjectManager       $manager
		 */
		public function __construct(GithubConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::ARMOR_DEFENSE);

			$this->manager = $manager;
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(array $context = []): void {
			$uri = $this->getUriWithPath('/armors/armor_base.csv');
			$result = $this->getWithRetry($uri);

			if ($result->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$reader = new CsvReader($result->getBody()->getContents(), [
				'defense_base' => 'intval',
				'defense_max' => 'intval',
				'defense_augment_max' => 'intval',
			]);

			while ($data = $reader->read()) {
				$name = str_replace([
					'α',
					'β',
					'γ',
				], [
					'Alpha',
					'Beta',
					'Gamma',
				], $data['name_en']);

				/** @var Armor|null $armor */
				$armor = $this->manager->getRepository('App:Armor')->findOneBy([
					'name' => $name,
				]);

				if (!$armor)
					throw new \RuntimeException('Could not find armor named ' . $name);

				$armor->getDefense()
					->setBase($data['defense_base'])
					->setMax($data['defense_max'])
					->setAugmented($data['defense_augment_max']);

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}
	}