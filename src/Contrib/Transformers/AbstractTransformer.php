<?php
	namespace App\Contrib\Transformers;

	use App\Contrib\Exceptions\IntegrityException;
	use App\Contrib\TransformerInterface;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\ORM\EntityManagerInterface;

	abstract class AbstractTransformer implements TransformerInterface {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var string
		 */
		protected $entityClass;

		/**
		 * AbstractTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param string                 $entityClass
		 */
		public function __construct(EntityManagerInterface $entityManager, string $entityClass) {
			$this->entityManager = $entityManager;
			$this->entityClass = $entityClass;
		}

		/**
		 * @return string
		 */
		public function getEntityClass(): string {
			return $this->entityClass;
		}

		/**
		 * @return \InvalidArgumentException
		 */
		protected function createEntityNotSupportedException(): \InvalidArgumentException {
			return new \InvalidArgumentException(
				'This transformer only supports ' . $this->getEntityClass() . ' entities'
			);
		}

		/**
		 * @param string     $path
		 * @param Collection $collection
		 * @param string     $class
		 * @param array      $ids
		 *
		 * @return void
		 */
		protected function populateFromIdArray(string $path, Collection $collection, string $class, array $ids) {
			$collection->clear();

			foreach ($ids as $index => $id) {
				$value = $this->entityManager->getRepository($class)->find($id);

				if (!$value) {
					$name = substr($class, strrpos($class, '\\') + 1);

					throw IntegrityException::missingReference($path . '[' . $index . ']', $name);
				}

				$collection->add($value);
			}
		}
	}