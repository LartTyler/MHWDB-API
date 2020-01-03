<?php

	namespace App\Localization;

	use App\Entity\EntityTrait;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	trait StringsEntityTrait {
		use EntityTrait;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\LanguageTag", "values"})
		 *
		 * @ORM\Column(type="string", length=7)
		 *
		 * @var string
		 * @see LanguageTag
		 */
		protected $language;

		/**
		 * @return string
		 * @see LanguageTag
		 */
		public function getLanguage(): string {
			return $this->language;
		}
	}