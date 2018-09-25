<?php
	namespace App\Entity;

	use App\Utility\StringUtil;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * Trait SluggableTrait
	 *
	 * @package App\Entity
	 *
	 * @deprecated
	 */
	trait SluggableTrait {
		/**
		 * @ORM\Column(type="string", length=64, nullable=false, unique=true)
		 *
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
		public function setSlug(string $slug) {
			$this->slug = StringUtil::toSlug($slug);

			return $this;
		}
	}