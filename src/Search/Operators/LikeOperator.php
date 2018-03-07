<?php
	namespace App\Search\Operators;

	use App\Search\Exception\InvalidFieldValueException;
	use App\Search\OperatorInterface;
	use App\Search\SearchQuery;
	use Doctrine\ORM\Query\Expr\Composite;

	class LikeOperator implements OperatorInterface {
		/**
		 * {@inheritdoc}
		 */
		public function process(SearchQuery $query, string $key, $argument, int &$paramIndex, Composite $node): void {
			if (!is_string($argument))
				throw new InvalidFieldValueException($key . '.$like', 'string');

			$node->add($query->expr()->like($query->resolveField($key), '?' . $paramIndex));
			$query->setParameter($paramIndex++, $argument);
		}
	}