<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait as BaseEntityTrait;
	use Doctrine\ORM\Mapping as ORM;

	trait EntityTrait {
		use BaseEntityTrait;

		/**
		 * @ORM\Id()
		 * @ORM\GeneratedValue()
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 *
		 * @var int|null
		 */
		protected $id;
	}