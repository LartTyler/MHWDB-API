<?php
	namespace App\Contrib;

	class TransformerManager {
		/**
		 * @var TransformerInterface[]
		 */
		protected $transformers = [];

		/**
		 * TransformerManager constructor.
		 *
		 * @param TransformerInterface[] $transformers
		 */
		public function __construct(array $transformers) {
			foreach ($transformers as $transformer)
				$this->transformers[$transformer->getEntityClass()] = $transformer;
		}

		/**
		 * @param string $class
		 *
		 * @return TransformerInterface|null
		 */
		public function getTransformer(string $class): ?TransformerInterface {
			return $this->transformers[$class] ?? null;
		}
	}