<?php
	namespace App\Search;

	use Doctrine\ORM\Query\Expr\Base;

	interface OperatorInterface {
		/**
		 * @param SearchQuery  $query
		 * @param string|array $argument
		 * @param int          $paramIndex
		 *
		 * @return Base
		 */
		public function process(SearchQuery $query, $argument, int &$paramIndex = 0): Base;
	}