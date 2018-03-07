<?php
	namespace App\Search\Operators;

	use App\Search\Exception\InvalidFieldValueException;
	use App\Search\OperatorInterface;
	use App\Search\SearchQuery;
	use Doctrine\ORM\Query\Expr\Composite;

	class InOperator implements OperatorInterface {
		/**
		 * {@inheritdoc}
		 */
		public function process(SearchQuery $query, string $key, $argument, int &$paramIndex, Composite $node): void {
			if (!is_array($argument))
				throw new InvalidFieldValueException($key . '.$in', 'array');

			$node->add($query->expr()->in($query->resolveField($key), '?' . $paramIndex));
			$query->setParameter($paramIndex++, $argument);
		}
	}