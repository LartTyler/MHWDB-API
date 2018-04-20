<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180420125649 extends AbstractMigration {
		public function up(Schema $schema) {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE armor_slots (armor_id INT UNSIGNED NOT NULL, slot_id INT UNSIGNED NOT NULL, INDEX IDX_24FFC0B8F5AA3663 (armor_id), INDEX IDX_24FFC0B859E5119C (slot_id), PRIMARY KEY(armor_id, slot_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE slots (id INT UNSIGNED AUTO_INCREMENT NOT NULL, rank SMALLINT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE armor_slots ADD CONSTRAINT FK_24FFC0B8F5AA3663 FOREIGN KEY (armor_id) REFERENCES armor (id)');
			$this->addSql('ALTER TABLE armor_slots ADD CONSTRAINT FK_24FFC0B859E5119C FOREIGN KEY (slot_id) REFERENCES slots (id)');
		}

		public function down(Schema $schema) {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE armor_slots DROP FOREIGN KEY FK_24FFC0B859E5119C');
			$this->addSql('DROP TABLE armor_slots');
			$this->addSql('DROP TABLE slots');
		}
	}
