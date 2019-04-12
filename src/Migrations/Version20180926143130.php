<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180926143130 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql','Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE motion_values CHANGE damage_type damage_type VARCHAR(32) DEFAULT NULL, CHANGE stun_potency stun_potency SMALLINT UNSIGNED DEFAULT NULL, CHANGE exhaust_potency exhaust_potency SMALLINT UNSIGNED DEFAULT NULL');
			$this->addSql('DROP INDEX uniq_bf27fefc989d9b62 ON armor');
			$this->addSql('ALTER TABLE armor DROP slug, CHANGE armor_set_id armor_set_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL, CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapon_assets CHANGE icon_id icon_id INT UNSIGNED DEFAULT NULL, CHANGE image_id image_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('DROP INDEX uniq_5b50f9ef989d9b62 ON charms');
			$this->addSql('ALTER TABLE charms DROP slug');
			$this->addSql('ALTER TABLE camps CHANGE location_id location_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE armor_sets CHANGE bonus_id bonus_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('DROP INDEX uniq_eece3328989d9b62 ON skill_ranks');
			$this->addSql('ALTER TABLE skill_ranks DROP slug');
			$this->addSql('ALTER TABLE weapon_crafting_info CHANGE previous_id previous_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('DROP INDEX uniq_b53777c8989d9b62 ON ailments');
			$this->addSql('ALTER TABLE ailments DROP slug');
			$this->addSql('ALTER TABLE charm_ranks CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('DROP INDEX uniq_53bb9ddd989d9b62 ON decorations');
			$this->addSql('ALTER TABLE decorations DROP slug');
			$this->addSql('ALTER TABLE armor_assets CHANGE image_male_id image_male_id INT UNSIGNED DEFAULT NULL, CHANGE image_female_id image_female_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('DROP INDEX uniq_d5311670989d9b62 ON skills');
			$this->addSql('ALTER TABLE skills DROP slug');
			$this->addSql('DROP INDEX uniq_520ebbe1989d9b62 ON weapons');
			$this->addSql('ALTER TABLE weapons DROP slug, CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql','Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE ailments ADD slug VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci');
			$this->addSql('CREATE UNIQUE INDEX uniq_b53777c8989d9b62 ON ailments (slug)');
			$this->addSql('ALTER TABLE armor ADD slug VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci, CHANGE armor_set_id armor_set_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL, CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('CREATE UNIQUE INDEX uniq_bf27fefc989d9b62 ON armor (slug)');
			$this->addSql('ALTER TABLE armor_assets CHANGE image_male_id image_male_id INT UNSIGNED DEFAULT NULL, CHANGE image_female_id image_female_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE armor_sets CHANGE bonus_id bonus_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE camps CHANGE location_id location_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE charm_ranks CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE charms ADD slug VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci');
			$this->addSql('CREATE UNIQUE INDEX uniq_5b50f9ef989d9b62 ON charms (slug)');
			$this->addSql('ALTER TABLE decorations ADD slug VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci');
			$this->addSql('CREATE UNIQUE INDEX uniq_53bb9ddd989d9b62 ON decorations (slug)');
			$this->addSql('ALTER TABLE motion_values CHANGE damage_type damage_type VARCHAR(32) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE stun_potency stun_potency SMALLINT UNSIGNED DEFAULT NULL, CHANGE exhaust_potency exhaust_potency SMALLINT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE skill_ranks ADD slug VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci');
			$this->addSql('CREATE UNIQUE INDEX uniq_eece3328989d9b62 ON skill_ranks (slug)');
			$this->addSql('ALTER TABLE skills ADD slug VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci');
			$this->addSql('CREATE UNIQUE INDEX uniq_d5311670989d9b62 ON skills (slug)');
			$this->addSql('ALTER TABLE weapon_assets CHANGE icon_id icon_id INT UNSIGNED DEFAULT NULL, CHANGE image_id image_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapon_crafting_info CHANGE previous_id previous_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapons ADD slug VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci, CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('CREATE UNIQUE INDEX uniq_520ebbe1989d9b62 ON weapons (slug)');
		}
	}
