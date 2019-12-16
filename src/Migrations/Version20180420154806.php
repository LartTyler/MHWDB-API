<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180420154806 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE armor ADD resist_fire INT NOT NULL, ADD resist_water INT NOT NULL, ADD resist_ice INT NOT NULL, ADD resist_thunder INT NOT NULL, ADD resist_dragon INT NOT NULL, CHANGE armor_set_id armor_set_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL, CHANGE attributes attributes JSON NOT NULL');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE armor DROP resist_fire, DROP resist_water, DROP resist_ice, DROP resist_thunder, DROP resist_dragon, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL, CHANGE armor_set_id armor_set_id INT UNSIGNED DEFAULT NULL, CHANGE attributes attributes LONGTEXT NOT NULL COLLATE utf8mb4_bin');
		}
	}
