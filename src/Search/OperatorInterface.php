<?php
	namespace App\Search;

	use Doctrine\ORM\Query\Expr\Composite;

	interface OperatorInterface {
		/**
		 * @param SearchQuery  $query
		 * @param string       $key
		 * @param string|array $argument
		 * @param int          $paramIndex
		 * @param Composite    $node
		 *
		 * @return void
		 */
		public function process(SearchQuery $query, string $key, $argument, int &$paramIndex, Composite $node): void;
	}