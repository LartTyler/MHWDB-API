<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use App\Entity\WeaponSharpness;
	use Doctrine\DBAL\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180422194647 extends AbstractMigration {
		public function up(Schema $schema) {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE weapon_sharpnesses (id INT UNSIGNED AUTO_INCREMENT NOT NULL, red INT UNSIGNED NOT NULL, orange INT UNSIGNED NOT NULL, yellow INT UNSIGNED NOT NULL, green INT UNSIGNED NOT NULL, blue INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE weapons ADD sharpness_id INT UNSIGNED NULL');
			$this->addSql('ALTER TABLE weapons ADD CONSTRAINT FK_520EBBE1537ED785 FOREIGN KEY (sharpness_id) REFERENCES weapon_sharpnesses (id)');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_520EBBE1537ED785 ON weapons (sharpness_id)');
		}

		public function down(Schema $schema) {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE weapons DROP FOREIGN KEY FK_520EBBE1537ED785');
			$this->addSql('DROP TABLE weapon_sharpnesses');
			$this->addSql('DROP INDEX UNIQ_520EBBE1537ED785 ON weapons');
			$this->addSql('ALTER TABLE weapons DROP sharpness_id');
		}
	}
