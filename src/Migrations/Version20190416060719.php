<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20190416060719 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('CREATE TABLE world_events (id INT UNSIGNED AUTO_INCREMENT NOT NULL, location_id INT UNSIGNED NOT NULL, name VARCHAR(128) NOT NULL, type VARCHAR(128) NOT NULL, platform VARCHAR(16) NOT NULL, start_timestamp DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_timestamp DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', quest_rank SMALLINT UNSIGNED NOT NULL, description LONGTEXT DEFAULT NULL, requirements LONGTEXT DEFAULT NULL, success_conditions LONGTEXT DEFAULT NULL, exclusive VARCHAR(16) DEFAULT NULL, INDEX IDX_C3B92A0964D218E (location_id), UNIQUE INDEX UNIQ_C3B92A093952D0CB5E237E066D1E9DF8 (platform, name, start_timestamp), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE world_events ADD CONSTRAINT FK_C3B92A0964D218E FOREIGN KEY (location_id) REFERENCES locations (id)');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('DROP TABLE world_events');
		}
	}
