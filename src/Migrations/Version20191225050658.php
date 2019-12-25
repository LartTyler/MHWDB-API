<?php

	declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20191225050658 extends AbstractMigration {
		public function getDescription(): string {
			return '';
		}

		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('CREATE TABLE armor_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, armor_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_D46961425E237E06 (name), INDEX IDX_D4696142F5AA3663 (armor_id), UNIQUE INDEX UNIQ_D4696142F5AA3663D4DB71B5 (armor_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('ALTER TABLE armor_strings ADD CONSTRAINT FK_D4696142F5AA3663 FOREIGN KEY (armor_id) REFERENCES armor (id)');
			$this->addSql('INSERT INTO armor_strings (armor_id, language, name) SELECT id, "en", name FROM armor');
			$this->addSql('DROP INDEX UNIQ_BF27FEFC5E237E06 ON armor');
			$this->addSql('ALTER TABLE armor DROP name');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('ALTER TABLE armor ADD name VARCHAR(64) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_BF27FEFC5E237E06 ON armor (name)');
			$this->addSql('UPDATE armor a SET a.name = as.name FROM armor a LEFT JOIN armor_strings as ON a.id = as.armor_id AND as.language = "en"');
			$this->addSql('DROP TABLE armor_strings');
		}
	}
