<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Armor;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\GithubConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\Type;
	use Doctrine\Common\Persistence\ObjectManager;
	use Psr\Http\Message\UriInterface;
	use Symfony\Component\HttpFoundation\Response;

	class CarlosFdezMHWorldDataDefenseValuesScraper extends AbstractScraper implements ProgressAwareInterface {
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
			$uri = $this->getUriWithPath('/armors/armor_data.json');
			$result = $this->getWithRetry($uri);

			if ($result->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$armorData = json_decode($result->getBody()->getContents(), true);

			$this->progressBar->append(sizeof($armorData));

			foreach ($armorData as $armorName => $data) {
				/** @var Armor|null $armor */
				$armor = $this->manager->getRepository('App:Armor')->findOneBy([
					'name' => $armorName,
				]);

				if (!$armor)
					throw new \RuntimeException('Could not find armor named ' . $armorName);

				$armor->getDefense()
					->setBase($data['defense_base'])
					->setMax($data['defense_max'])
					->setAugmented($data['defense_augment_max']);

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param string $path
		 *
		 * @return UriInterface
		 */
		protected function getUriWithPath(string $path): UriInterface {
			return $this->configuration->getBaseUri()->withPath('/CarlosFdez/MHWorldData/master/source_data/' .
				ltrim($path, '/'));
		}
	}