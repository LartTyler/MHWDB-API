<?php
	namespace App\Search\Operators;

	use App\Search\Exception\InvalidFieldValueException;
	use App\Search\OperatorInterface;
	use App\Search\SearchQuery;
	use Doctrine\ORM\Query\Expr\Composite;

	class NotEqualOperator implements OperatorInterface {
		/**
		 * {@inheritdoc}
		 */
		public function process(SearchQuery $query, string $key, $argument, int &$paramIndex, Composite $node): void {
			if (!is_scalar($argument))
				throw new InvalidFieldValueException($key . '.$neq', 'scalar');

			$node->add($query->expr()->neq($query->resolveField($key), '?' . $paramIndex));
			$query->setParameter($paramIndex++, $argument);
		}
	}