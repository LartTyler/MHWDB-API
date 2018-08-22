<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180820053757 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE weapon_durability (weapon_id INT UNSIGNED NOT NULL, sharpness_id INT UNSIGNED NOT NULL, INDEX IDX_F83322FA95B82273 (weapon_id), INDEX IDX_F83322FA537ED785 (sharpness_id), PRIMARY KEY(weapon_id, sharpness_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE weapon_durability ADD CONSTRAINT FK_F83322FA95B82273 FOREIGN KEY (weapon_id) REFERENCES weapons (id)');
			$this->addSql('ALTER TABLE weapon_durability ADD CONSTRAINT FK_F83322FA537ED785 FOREIGN KEY (sharpness_id) REFERENCES weapon_sharpnesses (id)');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('DROP TABLE weapon_durability');
		}
	}
