<?php
	namespace App\Search\Operators;

	use App\Search\OperatorInterface;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\ORM\Query\Expr\Base;

	class OrOperator implements OperatorInterface {
		public function getKey(): string {
			return 'or';
		}

		public function process($args): Base {
			if ($args instanceof Collection)
				$args = $args->toArray();
			else if (!is_array($args))
				throw new \InvalidArgumentException('Cannot OR using a ' . (is_object($args) ? get_class($args) :
						gettype($args)));
		}
	}