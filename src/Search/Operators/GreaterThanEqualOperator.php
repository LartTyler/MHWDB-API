<?php
	namespace App\Search\Operators;

	use App\Search\Exception\InvalidFieldValueException;
	use App\Search\OperatorInterface;
	use App\Search\SearchQuery;
	use Doctrine\ORM\Query\Expr\Composite;

	class GreaterThanEqualOperator implements OperatorInterface {
		/**
		 * {@inheritdoc}
		 */
		public function process(SearchQuery $query, string $key, $argument, int &$paramIndex, Composite $node): void {
			if (!is_int($argument))
				throw new InvalidFieldValueException($key . '.$gte', 'integer');

			$node->add($query->expr()->gte($query->resolveField($key), '?' . $paramIndex));
			$query->setParameter($paramIndex++, $argument);
		}
	}