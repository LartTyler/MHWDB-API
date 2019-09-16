<?php
	namespace App\Command;

	use App\Entity\Weapon;
	use App\Game\Attribute;

	class ExtractDamageTypeAttributeCommand extends AbstractExtractWeaponAttributeCommand {
		protected static $defaultName = 'app:tools:extract-damage-type-attributes';

		/**
		 * {@inheritdoc}
		 */
		protected function process(Weapon $weapon, bool $deleteAttribute): void {
			$damageType = $weapon->getAttribute(Attribute::DAMAGE_TYPE);

			if (!$damageType)
				return;

			$weapon->setDamageType($damageType);

			if ($deleteAttribute)
				$weapon->removeAttribute(Attribute::DAMAGE_TYPE);
		}
	}