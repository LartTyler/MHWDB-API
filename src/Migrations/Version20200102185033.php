<?php
	declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20200102185033 extends AbstractMigration {
		public function getDescription(): string {
			return '';
		}

		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			// Create translation tables
			$this->addSql('CREATE TABLE ailment_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, ailment_id INT UNSIGNED NOT NULL, name VARCHAR(32) NOT NULL, description LONGTEXT NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_CA3B2B475E237E06 (name), INDEX IDX_CA3B2B47432CD43A (ailment_id), UNIQUE INDEX UNIQ_CA3B2B47432CD43AD4DB71B5 (ailment_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE armor_set_bonus_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, armor_set_bonus_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_1E3E61D15E237E06 (name), INDEX IDX_1E3E61D1F0247870 (armor_set_bonus_id), UNIQUE INDEX UNIQ_1E3E61D1F0247870D4DB71B5 (armor_set_bonus_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE armor_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, armor_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_D46961425E237E06 (name), INDEX IDX_D4696142F5AA3663 (armor_id), UNIQUE INDEX UNIQ_D4696142F5AA3663D4DB71B5 (armor_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE location_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, location_id INT UNSIGNED NOT NULL, name VARCHAR(32) NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_5ADF79515E237E06 (name), INDEX IDX_5ADF795164D218E (location_id), UNIQUE INDEX UNIQ_5ADF795164D218ED4DB71B5 (location_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE monster_weakness_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, monster_weakness_id INT UNSIGNED NOT NULL, _condition LONGTEXT DEFAULT NULL, language VARCHAR(7) NOT NULL, INDEX IDX_44F4CE7BB86BD44E (monster_weakness_id), UNIQUE INDEX UNIQ_44F4CE7BB86BD44ED4DB71B5 (monster_weakness_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE camp_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, camp_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, language VARCHAR(7) NOT NULL, INDEX IDX_C409299B77075ABB (camp_id), UNIQUE INDEX UNIQ_C409299B77075ABBD4DB71B5 (camp_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE reward_condition_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, reward_condition_id INT UNSIGNED NOT NULL, subtype VARCHAR(128) DEFAULT NULL, language VARCHAR(7) NOT NULL, INDEX IDX_EFC817C3E0BFB5A3 (reward_condition_id), UNIQUE INDEX UNIQ_EFC817C3E0BFB5A3D4DB71B5 (reward_condition_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE weapon_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, weapon_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_11AD2AB15E237E06 (name), INDEX IDX_11AD2AB195B82273 (weapon_id), UNIQUE INDEX UNIQ_11AD2AB195B82273D4DB71B5 (weapon_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE charm_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, charm_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_F929C4AB5E237E06 (name), INDEX IDX_F929C4AB93E9261F (charm_id), UNIQUE INDEX UNIQ_F929C4AB93E9261FD4DB71B5 (charm_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE skill_rank_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, rank_id INT UNSIGNED NOT NULL, description LONGTEXT NOT NULL, language VARCHAR(7) NOT NULL, INDEX IDX_47A7FA57616678F (rank_id), UNIQUE INDEX UNIQ_47A7FA57616678FD4DB71B5 (rank_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE skill_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, skill_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_AE6FF4DC5E237E06 (name), INDEX IDX_AE6FF4DC5585C142 (skill_id), UNIQUE INDEX UNIQ_AE6FF4DC5585C142D4DB71B5 (skill_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE decoration_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, decoration_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_CF00E3BD5E237E06 (name), INDEX IDX_CF00E3BD3446DFC4 (decoration_id), UNIQUE INDEX UNIQ_CF00E3BD3446DFC4D4DB71B5 (decoration_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE armor_set_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, armor_set_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_CDE5C6F65E237E06 (name), INDEX IDX_CDE5C6F6537E6F87 (armor_set_id), UNIQUE INDEX UNIQ_CDE5C6F6537E6F87D4DB71B5 (armor_set_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE monster_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, monster_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT DEFAULT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_16FBDBCA5E237E06 (name), INDEX IDX_16FBDBCAC5FF1223 (monster_id), UNIQUE INDEX UNIQ_16FBDBCAC5FF1223D4DB71B5 (monster_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE item_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, item_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_BFC74CA15E237E06 (name), INDEX IDX_BFC74CA1126F525E (item_id), UNIQUE INDEX UNIQ_BFC74CA1126F525ED4DB71B5 (item_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE motion_value_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, motion_value_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, language VARCHAR(7) NOT NULL, INDEX IDX_5735600541F7F30D (motion_value_id), UNIQUE INDEX UNIQ_5735600541F7F30DD4DB71B5 (motion_value_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE world_event_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, event_id INT UNSIGNED NOT NULL, name VARCHAR(128) NOT NULL, description LONGTEXT DEFAULT NULL, requirements LONGTEXT DEFAULT NULL, success_conditions LONGTEXT DEFAULT NULL, language VARCHAR(7) NOT NULL, INDEX IDX_50C355E271F7E88B (event_id), UNIQUE INDEX UNIQ_50C355E271F7E88BD4DB71B5 (event_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('ALTER TABLE ailment_strings ADD CONSTRAINT FK_CA3B2B47432CD43A FOREIGN KEY (ailment_id) REFERENCES ailments (id)');
			$this->addSql('ALTER TABLE armor_set_bonus_strings ADD CONSTRAINT FK_1E3E61D1F0247870 FOREIGN KEY (armor_set_bonus_id) REFERENCES armor_set_bonuses (id)');
			$this->addSql('ALTER TABLE armor_strings ADD CONSTRAINT FK_D4696142F5AA3663 FOREIGN KEY (armor_id) REFERENCES armor (id)');
			$this->addSql('ALTER TABLE location_strings ADD CONSTRAINT FK_5ADF795164D218E FOREIGN KEY (location_id) REFERENCES locations (id)');
			$this->addSql('ALTER TABLE monster_weakness_strings ADD CONSTRAINT FK_44F4CE7BB86BD44E FOREIGN KEY (monster_weakness_id) REFERENCES monster_weaknesses (id)');
			$this->addSql('ALTER TABLE camp_strings ADD CONSTRAINT FK_C409299B77075ABB FOREIGN KEY (camp_id) REFERENCES camps (id)');
			$this->addSql('ALTER TABLE reward_condition_strings ADD CONSTRAINT FK_EFC817C3E0BFB5A3 FOREIGN KEY (reward_condition_id) REFERENCES reward_conditions (id)');
			$this->addSql('ALTER TABLE weapon_strings ADD CONSTRAINT FK_11AD2AB195B82273 FOREIGN KEY (weapon_id) REFERENCES weapons (id)');
			$this->addSql('ALTER TABLE charm_strings ADD CONSTRAINT FK_F929C4AB93E9261F FOREIGN KEY (charm_id) REFERENCES charms (id)');
			$this->addSql('ALTER TABLE skill_rank_strings ADD CONSTRAINT FK_47A7FA57616678F FOREIGN KEY (rank_id) REFERENCES skill_ranks (id)');
			$this->addSql('ALTER TABLE skill_strings ADD CONSTRAINT FK_AE6FF4DC5585C142 FOREIGN KEY (skill_id) REFERENCES skills (id)');
			$this->addSql('ALTER TABLE decoration_strings ADD CONSTRAINT FK_CF00E3BD3446DFC4 FOREIGN KEY (decoration_id) REFERENCES decorations (id)');
			$this->addSql('ALTER TABLE armor_set_strings ADD CONSTRAINT FK_CDE5C6F6537E6F87 FOREIGN KEY (armor_set_id) REFERENCES armor_sets (id)');
			$this->addSql('ALTER TABLE monster_strings ADD CONSTRAINT FK_16FBDBCAC5FF1223 FOREIGN KEY (monster_id) REFERENCES monsters (id)');
			$this->addSql('ALTER TABLE item_strings ADD CONSTRAINT FK_BFC74CA1126F525E FOREIGN KEY (item_id) REFERENCES items (id)');
			$this->addSql('ALTER TABLE motion_value_strings ADD CONSTRAINT FK_5735600541F7F30D FOREIGN KEY (motion_value_id) REFERENCES motion_values (id)');
			$this->addSql('ALTER TABLE world_event_strings ADD CONSTRAINT FK_50C355E271F7E88B FOREIGN KEY (event_id) REFERENCES world_events (id)');

			// Copy strings to new tables
			$this->addSql('INSERT INTO ailment_strings (ailment_id, language, name, description) SELECT id, "en", name, description FROM ailments');
			$this->addSql('INSERT INTO armor_set_bonus_strings (armor_set_bonus_id, language, name) SELECT id, "en", name FROM armor_set_bonuses');
			$this->addSql('INSERT INTO armor_strings (armor_id, language, name) SELECT id, "en", name FROM armor');
			$this->addSql('INSERT INTO location_strings (location_id, language, name) SELECT id, "en", name FROM locations');
			$this->addSql('INSERT INTO monster_weakness_strings (monster_weakness_id, language, _condition) SELECT id, "en", _condition FROM monster_weaknesses');
			$this->addSql('INSERT INTO camp_strings (camp_id, language, name) SELECT id, "en", name FROM camps');
			$this->addSql('INSERT INTO reward_condition_strings (reward_condition_id, language, subtype) SELECT id, "en", subtype FROM reward_conditions');
			$this->addSql('INSERT INTO weapon_strings (weapon_id, language, name) SELECT id, "en", name FROM weapons');
			$this->addSql('INSERT INTO charm_strings (charm_id, language, name) SELECT id, "en", name FROM charms');
			$this->addSql('INSERT INTO skill_rank_strings (rank_id, language, description) SELECT id, "en", description FROM skill_ranks');
			$this->addSql('INSERT INTO skill_strings (skill_id, language, name, description) SELECT id, "en", name, description FROM skills');
			$this->addSql('INSERT INTO decoration_strings (decoration_id, language, name) SELECT id, "en", name FROM decorations');
			$this->addSql('INSERT INTO armor_set_strings (armor_set_id, language, name) SELECT id, "en", name FROM armor_sets');
			$this->addSql('INSERT INTO monster_strings (monster_id, language, name, description) SELECT id, "en", name, description FROM monsters');
			$this->addSql('INSERT INTO item_strings (item_id, language, name, description) SELECT id, "en", name, description FROM items');
			$this->addSql('INSERT INTO motion_value_strings (motion_value_id, language, name) SELECT id, "en", name FROM motion_values');
			$this->addSql('INSERT INTO world_event_strings (event_id, language, name, description, requirements, success_conditions) SELECT id, "en", name, description, requirements, success_conditions FROM world_events');

			// Remove strings columns from original tables
			$this->addSql('DROP INDEX UNIQ_C3B92A093952D0CB5E237E06F0695B726D1E9DF8 ON world_events');
			$this->addSql('ALTER TABLE world_events DROP name, DROP description, DROP requirements, DROP success_conditions, CHANGE exclusive exclusive VARCHAR(16) DEFAULT NULL, CHANGE expansion expansion VARCHAR(32) NOT NULL, CHANGE master_rank master_rank TINYINT(1) NOT NULL');
			$this->addSql('DROP INDEX UNIQ_BF27FEFC5E237E06 ON armor');
			$this->addSql('ALTER TABLE armor DROP name, CHANGE armor_set_id armor_set_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL, CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapon_crafting_info CHANGE previous_id previous_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE skill_ranks DROP description');
			$this->addSql('ALTER TABLE weapon_phials CHANGE damage damage SMALLINT UNSIGNED DEFAULT NULL');
			$this->addSql('DROP INDEX UNIQ_B53777C85E237E06 ON ailments');
			$this->addSql('ALTER TABLE ailments DROP name, DROP description');
			$this->addSql('ALTER TABLE armor_assets CHANGE image_male_id image_male_id INT UNSIGNED DEFAULT NULL, CHANGE image_female_id image_female_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('DROP INDEX UNIQ_520EBBE15E237E06 ON weapons');
			$this->addSql('ALTER TABLE weapons DROP name, CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL, CHANGE phial_id phial_id INT UNSIGNED DEFAULT NULL, CHANGE elderseal elderseal VARCHAR(16) DEFAULT NULL, CHANGE coatings coatings LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', CHANGE special_ammo special_ammo VARCHAR(32) DEFAULT NULL, CHANGE deviation deviation VARCHAR(32) DEFAULT NULL, CHANGE boost_type boost_type VARCHAR(32) DEFAULT NULL, CHANGE damage_type damage_type VARCHAR(32) DEFAULT NULL');
			$this->addSql('DROP INDEX UNIQ_17E64ABA5E237E06 ON locations');
			$this->addSql('ALTER TABLE locations DROP name');
			$this->addSql('DROP INDEX UNIQ_A1FAA7C85E237E06 ON monsters');
			$this->addSql('ALTER TABLE monsters DROP name, DROP description');
			$this->addSql('DROP INDEX UNIQ_5B50F9EF5E237E06 ON charms');
			$this->addSql('ALTER TABLE charms DROP name');
			$this->addSql('DROP INDEX UNIQ_53BB9DDD5E237E06 ON decorations');
			$this->addSql('ALTER TABLE decorations DROP name');
			$this->addSql('DROP INDEX UNIQ_672789F734C1BFD65E237E06 ON motion_values');
			$this->addSql('ALTER TABLE motion_values DROP name, CHANGE damage_type damage_type VARCHAR(32) DEFAULT NULL, CHANGE stun_potency stun_potency SMALLINT UNSIGNED DEFAULT NULL, CHANGE exhaust_potency exhaust_potency SMALLINT UNSIGNED DEFAULT NULL');
			$this->addSql('DROP INDEX UNIQ_D53116705E237E06 ON skills');
			$this->addSql('ALTER TABLE skills DROP name, DROP description');
			$this->addSql('ALTER TABLE camps DROP name, CHANGE location_id location_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE charm_ranks CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE reward_conditions DROP subtype');
			$this->addSql('DROP INDEX UNIQ_FC4A63D15E237E06 ON armor_set_bonuses');
			$this->addSql('ALTER TABLE armor_set_bonuses DROP name');
			$this->addSql('DROP INDEX UNIQ_E11EE94D5E237E06 ON items');
			$this->addSql('ALTER TABLE items DROP name, DROP description');
			$this->addSql('ALTER TABLE monster_weaknesses DROP _condition');
			$this->addSql('DROP INDEX UNIQ_7C8A0B105E237E06 ON armor_sets');
			$this->addSql('ALTER TABLE armor_sets DROP name, CHANGE bonus_id bonus_id INT UNSIGNED DEFAULT NULL');
		}

		public function down(Schema $schema): void {
			$this->throwIrreversibleMigrationException();
		}
	}
