<?php
	namespace App\Search\Operators;

	use App\Search\Exception\InvalidFieldValueException;
	use App\Search\OperatorInterface;
	use App\Search\SearchQuery;
	use Doctrine\ORM\Query\Expr\Composite;

	class LessThanOperator implements OperatorInterface {
		/**
		 * {@inheritdoc}
		 */
		public function process(SearchQuery $query, string $key, $argument, int &$paramIndex, Composite $node): void {
			if (!is_int($argument))
				throw new InvalidFieldValueException($key . '.$lt', 'integer');

			$node->add($query->expr()->lt($query->resolveField($key), '?' . $paramIndex));
			$query->setParameter($paramIndex++, $argument);
		}
	}