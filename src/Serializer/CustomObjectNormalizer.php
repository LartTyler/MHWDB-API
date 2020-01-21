<?php
	namespace App\Serializer;

	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

	class CustomObjectNormalizer extends ObjectNormalizer {
		/**
		 * {@inheritdoc}
		 */
		public function normalize($object, string $format = null, array $context = []) {
			$context[ObjectNormalizer::PRESERVE_EMPTY_OBJECTS] =
				$context[ObjectNormalizer::PRESERVE_EMPTY_OBJECTS] ??
				$this->defaultContext[ObjectNormalizer::PRESERVE_EMPTY_OBJECTS];

			return parent::normalize($object, $format, $context);
		}
	}