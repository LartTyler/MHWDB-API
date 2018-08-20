<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Weapon;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\MHWGConfiguration;
	use Doctrine\Common\Persistence\ObjectManager;

	abstract class AbstractMHWGScraper extends AbstractScraper {
		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * AbstractMHWGScraper constructor.
		 *
		 * @param MHWGConfiguration $configuration
		 * @param string            $type
		 * @param ObjectManager     $manager
		 */
		public function __construct(MHWGConfiguration $configuration, string $type, ObjectManager $manager) {
			parent::__construct($configuration, $type);

			$this->manager = $manager;
		}

		/**
		 * @param string $weaponType
		 * @param int    $index
		 *
		 * @return Weapon|null
		 */
		protected function matchWeapon(string $weaponType, int $index): ?Weapon {
			$results = $this->manager->getRepository('App:Weapon')->findBy([
				'type' => $weaponType,
			], [
				'id' => 'ASC',
			], 1, $index);

			return $results[0] ?? null;
		}
	}