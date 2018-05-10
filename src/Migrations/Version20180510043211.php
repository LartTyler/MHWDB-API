<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180510043211 extends AbstractMigration {
		public function up(Schema $schema) {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE motion_values (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, weapon_type VARCHAR(32) NOT NULL, damage_type VARCHAR(32) DEFAULT NULL, stun_potency SMALLINT UNSIGNED DEFAULT NULL, exhaust_potency SMALLINT UNSIGNED DEFAULT NULL, hits JSON NOT NULL, UNIQUE INDEX name_weapon_type_idx (weapon_type, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
		}

		public function down(Schema $schema) {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('DROP TABLE motion_values');
		}
	}
