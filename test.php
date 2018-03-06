<?php
	require __DIR__ . '/vendor/autoload.php';

	use Doctrine\ORM\Query\Expr\Andx;
	use Doctrine\ORM\Query\Expr\Orx;

	$query = [
		'field' => 'value',
		'otherField' => 'otherValue',
		'$or' => [
			[
				'field' => 1,
			],
			[
				'field' => 2,
			]
		]
	];

	var_dump((string)parse($query));

	function parse(array $parts, int &$argIndex = 0) {
		$clause = new Andx();

		foreach ($parts as $key => $part) {
			if (strpos($key, '$') === 0) {
				$keyword = substr($key, 1);

				switch ($keyword) {
					case 'or':
						if (!is_array($part))
							throw new \InvalidArgumentException('$or value must be an array');

						$orX = new Orx();

						foreach ($part as $item)
							$orX->add(parse($item, $argIndex));

						if ($orX->count() === 0)
							throw new \InvalidArgumentException('$or value must not be an empty array');

						$clause->add($orX);

						break;
				}
			} else
				$clause->add($key . ' = :arg_' . $argIndex++);
		}

		return $clause;
	}