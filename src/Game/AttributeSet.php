<?php
	namespace App\Game;

	use App\Entity\AttributableTrait;

	class AttributeSet {
		use AttributableTrait;

		/**
		 * AttributeSet constructor.
		 *
		 * @param array $attributes
		 */
		public function __construct(array $attributes = []) {
			$this->setAttributes($attributes);
		}
	}