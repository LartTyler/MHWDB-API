<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20190815211202 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('CREATE TABLE weapon_ammo (id INT UNSIGNED AUTO_INCREMENT NOT NULL, weapon_id INT UNSIGNED NOT NULL, type VARCHAR(32) NOT NULL, capacities LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_579096D595B82273 (weapon_id), INDEX IDX_579096D58CDE5729 (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE weapon_ammo ADD CONSTRAINT FK_579096D595B82273 FOREIGN KEY (weapon_id) REFERENCES weapons (id)');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('DROP TABLE weapon_ammo');
		}
	}
