<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180827032437 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE camps (id INT UNSIGNED AUTO_INCREMENT NOT NULL, location_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, zone SMALLINT UNSIGNED NOT NULL, INDEX IDX_3D166BE564D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE locations (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, zone_count SMALLINT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_17E64ABA5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE camps ADD CONSTRAINT FK_3D166BE564D218E FOREIGN KEY (location_id) REFERENCES locations (id)');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE camps DROP FOREIGN KEY FK_3D166BE564D218E');
			$this->addSql('DROP TABLE camps');
			$this->addSql('DROP TABLE locations');
		}
	}
