<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180829235530 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE monsters (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, type VARCHAR(32) NOT NULL, species VARCHAR(32) NOT NULL, description LONGTEXT DEFAULT NULL, elements LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_A1FAA7C85E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE monster_ailments (monster_id INT UNSIGNED NOT NULL, ailment_id INT UNSIGNED NOT NULL, INDEX IDX_3F33B44C5FF1223 (monster_id), INDEX IDX_3F33B44432CD43A (ailment_id), PRIMARY KEY(monster_id, ailment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE monster_locations (monster_id INT UNSIGNED NOT NULL, location_id INT UNSIGNED NOT NULL, INDEX IDX_F35E41FDC5FF1223 (monster_id), INDEX IDX_F35E41FD64D218E (location_id), PRIMARY KEY(monster_id, location_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE monster_ailments ADD CONSTRAINT FK_3F33B44C5FF1223 FOREIGN KEY (monster_id) REFERENCES monsters (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE monster_ailments ADD CONSTRAINT FK_3F33B44432CD43A FOREIGN KEY (ailment_id) REFERENCES ailments (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE monster_locations ADD CONSTRAINT FK_F35E41FDC5FF1223 FOREIGN KEY (monster_id) REFERENCES monsters (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE monster_locations ADD CONSTRAINT FK_F35E41FD64D218E FOREIGN KEY (location_id) REFERENCES locations (id) ON DELETE CASCADE');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE monster_ailments DROP FOREIGN KEY FK_3F33B44C5FF1223');
			$this->addSql('ALTER TABLE monster_locations DROP FOREIGN KEY FK_F35E41FDC5FF1223');
			$this->addSql('DROP TABLE monsters');
			$this->addSql('DROP TABLE monster_ailments');
			$this->addSql('DROP TABLE monster_locations');
		}
	}
