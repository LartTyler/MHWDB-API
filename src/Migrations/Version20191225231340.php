<?php

	declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20191225231340 extends AbstractMigration {
		public function getDescription(): string {
			return '';
		}

		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('CREATE TABLE charm_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, charm_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_F929C4AB5E237E06 (name), INDEX IDX_F929C4AB93E9261F (charm_id), UNIQUE INDEX UNIQ_F929C4AB93E9261FD4DB71B5 (charm_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE camp_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, camp_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, language VARCHAR(7) NOT NULL, INDEX IDX_C409299B77075ABB (camp_id), UNIQUE INDEX UNIQ_C409299B77075ABBD4DB71B5 (camp_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE armor_set_bonus_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, armor_set_bonus_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_1E3E61D15E237E06 (name), INDEX IDX_1E3E61D1F0247870 (armor_set_bonus_id), UNIQUE INDEX UNIQ_1E3E61D1F0247870D4DB71B5 (armor_set_bonus_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('ALTER TABLE charm_strings ADD CONSTRAINT FK_F929C4AB93E9261F FOREIGN KEY (charm_id) REFERENCES charms (id)');
			$this->addSql('ALTER TABLE camp_strings ADD CONSTRAINT FK_C409299B77075ABB FOREIGN KEY (camp_id) REFERENCES camps (id)');
			$this->addSql('ALTER TABLE armor_set_bonus_strings ADD CONSTRAINT FK_1E3E61D1F0247870 FOREIGN KEY (armor_set_bonus_id) REFERENCES armor_set_bonuses (id)');
			$this->addSql('DROP INDEX UNIQ_FC4A63D15E237E06 ON armor_set_bonuses');
			$this->addSql('INSERT INTO armor_set_bonus_strings (armor_set_bonus_id, language, name) SELECT id, "en", name FROM armor_set_bonuses');
			$this->addSql('ALTER TABLE armor_set_bonuses DROP name');
			$this->addSql('DROP INDEX UNIQ_5B50F9EF5E237E06 ON charms');
			$this->addSql('INSERT INTO charm_strings (charm_id, language, name) SELECT id, "en", name FROM charms');
			$this->addSql('ALTER TABLE charms DROP name');
			$this->addSql('INSERT INTO camp_strings (camp_id, language, name) SELECT id, "en", name FROM camps');
			$this->addSql('ALTER TABLE camps DROP name');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('ALTER TABLE armor_set_bonuses ADD name VARCHAR(64) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_FC4A63D15E237E06 ON armor_set_bonuses (name)');
			$this->addSql('ALTER TABLE camps ADD name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
			$this->addSql('ALTER TABLE charms ADD name VARCHAR(64) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_5B50F9EF5E237E06 ON charms (name)');
			$this->addSql('UPDATE charms c SET c.name = cs.name FROM charms c LEFT JOIN charm_strings cs ON c.id = cs.charm_id AND cs.language = "en"');
			$this->addSql('DROP TABLE charm_strings');
			$this->addSql('UPDATE camps c SET c.name = cs.name FROM camps c LEFT JOIN camp_strings cs ON c.id = cs.camp_id AND cs.language = "en"');
			$this->addSql('DROP TABLE camp_strings');
			$this->addSql('UPDATE armor_set_bonuses a SET a.name = as.name FROM armor_set_bonuses a LEFT JOIN armor_set_bonus_strings as ON a.id = as.armor_set_bonus_id AND as.language = "en"');
			$this->addSql('DROP TABLE armor_set_bonus_strings');
		}
	}
