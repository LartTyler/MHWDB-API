<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180420144556 extends AbstractMigration {
		public function up(Schema $schema) {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE weapon_slots (weapon_id INT UNSIGNED NOT NULL, slot_id INT UNSIGNED NOT NULL, INDEX IDX_E5CEC5B595B82273 (weapon_id), INDEX IDX_E5CEC5B559E5119C (slot_id), PRIMARY KEY(weapon_id, slot_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE weapon_slots ADD CONSTRAINT FK_E5CEC5B595B82273 FOREIGN KEY (weapon_id) REFERENCES weapons (id)');
			$this->addSql('ALTER TABLE weapon_slots ADD CONSTRAINT FK_E5CEC5B559E5119C FOREIGN KEY (slot_id) REFERENCES slots (id)');
		}

		public function down(Schema $schema) {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('DROP TABLE weapon_slots');
		}
	}
