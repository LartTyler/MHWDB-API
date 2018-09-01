<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180901055339 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE contributions (id INT UNSIGNED AUTO_INCREMENT NOT NULL, submitted_by_id INT UNSIGNED NOT NULL, entity VARCHAR(64) NOT NULL, submitted_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_76391EFE79F7D87D (submitted_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE contribution_changes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, contribution_id INT UNSIGNED NOT NULL, type VARCHAR(16) NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_6101C8FCFE5E5FBD (contribution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE users (id INT UNSIGNED AUTO_INCREMENT NOT NULL, email VARCHAR(254) NOT NULL, username VARCHAR(32) NOT NULL, password VARCHAR(64) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE user_roles (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_54FCD59FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE contributions ADD CONSTRAINT FK_76391EFE79F7D87D FOREIGN KEY (submitted_by_id) REFERENCES users (id)');
			$this->addSql('ALTER TABLE contribution_changes ADD CONSTRAINT FK_6101C8FCFE5E5FBD FOREIGN KEY (contribution_id) REFERENCES contributions (id)');
			$this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE contribution_changes DROP FOREIGN KEY FK_6101C8FCFE5E5FBD');
			$this->addSql('ALTER TABLE contributions DROP FOREIGN KEY FK_76391EFE79F7D87D');
			$this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FA76ED395');
			$this->addSql('DROP TABLE contributions');
			$this->addSql('DROP TABLE contribution_changes');
			$this->addSql('DROP TABLE users');
			$this->addSql('DROP TABLE user_roles');
		}
	}
