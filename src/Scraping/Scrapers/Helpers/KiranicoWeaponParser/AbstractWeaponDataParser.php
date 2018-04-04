<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser;

	abstract class AbstractWeaponDataParser implements WeaponDataParserInterface {
		/**
		 * @var WeaponDataInterpreterInterface[]
		 */
		protected $interpreters = [];

		/**
		 * AbstractWeaponDataParser constructor.
		 *
		 * @param array $interpreters
		 */
		public function __construct(array $interpreters = []) {
			$this->setInterpreters($interpreters);
		}

		/**
		 * @param WeaponDataInterpreterInterface[] $interpreters
		 *
		 * @return $this
		 */
		public function setInterpreters(array $interpreters) {
			$this->interpreters = [];

			foreach ($interpreters as $interpreter)
				$this->addInterpreter($interpreter);

			return $this;
		}

		/**
		 * @param WeaponDataInterpreterInterface $interpreter
		 *
		 * @return $this
		 */
		public function addInterpreter(WeaponDataInterpreterInterface $interpreter) {
			$this->interpreters[] = $interpreter;

			return $this;
		}
	}