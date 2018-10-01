<?php declare(strict_types=1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180927205623 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE motion_values CHANGE damage_type damage_type VARCHAR(32) DEFAULT NULL, CHANGE stun_potency stun_potency SMALLINT UNSIGNED DEFAULT NULL, CHANGE exhaust_potency exhaust_potency SMALLINT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE armor CHANGE armor_set_id armor_set_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL, CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapon_assets CHANGE icon_id icon_id INT UNSIGNED DEFAULT NULL, CHANGE image_id image_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE camps CHANGE location_id location_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE armor_sets CHANGE bonus_id bonus_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapon_crafting_info CHANGE previous_id previous_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE charm_ranks CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE armor_slots CHANGE rank rank SMALLINT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE armor_assets CHANGE image_male_id image_male_id INT UNSIGNED DEFAULT NULL, CHANGE image_female_id image_female_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapon_slots CHANGE rank rank SMALLINT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE weapons CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE armor CHANGE armor_set_id armor_set_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL, CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE armor_assets CHANGE image_male_id image_male_id INT UNSIGNED DEFAULT NULL, CHANGE image_female_id image_female_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE armor_sets CHANGE bonus_id bonus_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE armor_slots CHANGE rank rank INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE camps CHANGE location_id location_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE charm_ranks CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE motion_values CHANGE damage_type damage_type VARCHAR(32) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE stun_potency stun_potency SMALLINT UNSIGNED DEFAULT NULL, CHANGE exhaust_potency exhaust_potency SMALLINT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapon_assets CHANGE icon_id icon_id INT UNSIGNED DEFAULT NULL, CHANGE image_id image_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapon_crafting_info CHANGE previous_id previous_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapon_slots CHANGE rank rank INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE weapons CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL');
		}
	}
