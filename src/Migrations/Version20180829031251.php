<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180829031251 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE ailments (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, description LONGTEXT NOT NULL, slug VARCHAR(64) NOT NULL, UNIQUE INDEX UNIQ_B53777C85E237E06 (name), UNIQUE INDEX UNIQ_B53777C8989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE ailment_protection_methods (id INT UNSIGNED AUTO_INCREMENT NOT NULL, ailment_id INT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_5DBD6746432CD43A (ailment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE ailment_protection_skills (ailment_protection_id INT UNSIGNED NOT NULL, skill_id INT UNSIGNED NOT NULL, INDEX IDX_517E18BFFE812F5B (ailment_protection_id), INDEX IDX_517E18BF5585C142 (skill_id), PRIMARY KEY(ailment_protection_id, skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE ailment_protection_items (ailment_protection_id INT UNSIGNED NOT NULL, item_id INT UNSIGNED NOT NULL, INDEX IDX_1051F85EFE812F5B (ailment_protection_id), INDEX IDX_1051F85E126F525E (item_id), PRIMARY KEY(ailment_protection_id, item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE ailment_recovery_methods (id INT UNSIGNED AUTO_INCREMENT NOT NULL, ailment_id INT UNSIGNED NOT NULL, actions LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_4C6AEAE5432CD43A (ailment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE ailment_recovery_items (ailment_recovery_id INT UNSIGNED NOT NULL, item_id INT UNSIGNED NOT NULL, INDEX IDX_400DCBFE8B04118A (ailment_recovery_id), INDEX IDX_400DCBFE126F525E (item_id), PRIMARY KEY(ailment_recovery_id, item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE ailment_protection_methods ADD CONSTRAINT FK_5DBD6746432CD43A FOREIGN KEY (ailment_id) REFERENCES ailments (id)');
			$this->addSql('ALTER TABLE ailment_protection_skills ADD CONSTRAINT FK_517E18BFFE812F5B FOREIGN KEY (ailment_protection_id) REFERENCES ailment_protection_methods (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE ailment_protection_skills ADD CONSTRAINT FK_517E18BF5585C142 FOREIGN KEY (skill_id) REFERENCES skills (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE ailment_protection_items ADD CONSTRAINT FK_1051F85EFE812F5B FOREIGN KEY (ailment_protection_id) REFERENCES ailment_protection_methods (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE ailment_protection_items ADD CONSTRAINT FK_1051F85E126F525E FOREIGN KEY (item_id) REFERENCES items (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE ailment_recovery_methods ADD CONSTRAINT FK_4C6AEAE5432CD43A FOREIGN KEY (ailment_id) REFERENCES ailments (id)');
			$this->addSql('ALTER TABLE ailment_recovery_items ADD CONSTRAINT FK_400DCBFE8B04118A FOREIGN KEY (ailment_recovery_id) REFERENCES ailment_recovery_methods (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE ailment_recovery_items ADD CONSTRAINT FK_400DCBFE126F525E FOREIGN KEY (item_id) REFERENCES items (id) ON DELETE CASCADE');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE ailment_protection_methods DROP FOREIGN KEY FK_5DBD6746432CD43A');
			$this->addSql('ALTER TABLE ailment_recovery_methods DROP FOREIGN KEY FK_4C6AEAE5432CD43A');
			$this->addSql('ALTER TABLE ailment_protection_skills DROP FOREIGN KEY FK_517E18BFFE812F5B');
			$this->addSql('ALTER TABLE ailment_protection_items DROP FOREIGN KEY FK_1051F85EFE812F5B');
			$this->addSql('ALTER TABLE ailment_recovery_items DROP FOREIGN KEY FK_400DCBFE8B04118A');
			$this->addSql('DROP TABLE ailments');
			$this->addSql('DROP TABLE ailment_protection_methods');
			$this->addSql('DROP TABLE ailment_protection_skills');
			$this->addSql('DROP TABLE ailment_protection_items');
			$this->addSql('DROP TABLE ailment_recovery_methods');
			$this->addSql('DROP TABLE ailment_recovery_items');
		}
	}
