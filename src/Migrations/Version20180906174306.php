<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180906174306 extends AbstractMigration {
		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('DROP INDEX UNIQ_1483A5E9D5499347 ON users');
			$this->addSql('ALTER TABLE users CHANGE display_name username VARCHAR(32) NOT NULL COLLATE utf8mb4_unicode_ci');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
		}

		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('DROP INDEX UNIQ_1483A5E9F85E0677 ON users');
			$this->addSql('ALTER TABLE users CHANGE username display_name VARCHAR(32) NOT NULL');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9D5499347 ON users (display_name)');
		}
	}
