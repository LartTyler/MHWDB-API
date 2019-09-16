<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20190916211506 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('CREATE TABLE weapon_shelling (id INT UNSIGNED AUTO_INCREMENT NOT NULL, weapon_id INT UNSIGNED NOT NULL, type VARCHAR(16) NOT NULL, level SMALLINT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_9415F73295B82273 (weapon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE weapon_shelling ADD CONSTRAINT FK_9415F73295B82273 FOREIGN KEY (weapon_id) REFERENCES weapons (id)');
			$this->addSql('ALTER TABLE weapons ADD boost_type VARCHAR(32) DEFAULT NULL, ADD damage_type VARCHAR(32) DEFAULT NULL');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('DROP TABLE weapon_shelling');
			$this->addSql('ALTER TABLE weapons DROP boost_type, DROP damage_type');
		}
	}
