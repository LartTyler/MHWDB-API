<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20190815181742 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('CREATE TABLE weapon_phials (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(32) NOT NULL, damage SMALLINT UNSIGNED DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE weapons ADD phial_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapons ADD CONSTRAINT FK_520EBBE11272188D FOREIGN KEY (phial_id) REFERENCES weapon_phials (id)');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_520EBBE11272188D ON weapons (phial_id)');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('ALTER TABLE weapons DROP FOREIGN KEY FK_520EBBE11272188D');
			$this->addSql('DROP TABLE weapon_phials');
			$this->addSql('DROP INDEX UNIQ_520EBBE11272188D ON weapons');
			$this->addSql('ALTER TABLE weapons DROP phial_id');
		}
	}
