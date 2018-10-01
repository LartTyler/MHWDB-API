<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180906174847 extends AbstractMigration {
		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('DROP INDEX UNIQ_54FCD59FA76ED3955E237E06 ON user_roles');
			$this->addSql('ALTER TABLE user_roles DROP name');
		}

		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE user_roles ADD name VARCHAR(32) NOT NULL');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_54FCD59FA76ED3955E237E06 ON user_roles (user_id, name)');
		}
	}
