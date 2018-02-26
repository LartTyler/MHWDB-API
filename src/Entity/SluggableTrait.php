<?php
	namespace App\Entity;

	use App\Utility\StringUtil;

	trait SluggableTrait {
		/**
		 * @var string
		 */
		protected $slug;

		/**
		 * @return string
		 */
		public function getSlug(): string {
			return $this->slug;
		}

		/**
		 * @param string $slug
		 *
		 * @return $this
		 */
		protected function setSlug(string $slug) {
			$this->slug = StringUtil::toSlug($slug);

			return $this;
		}
	}