<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180511152554 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE decorations DROP FOREIGN KEY FK_53BB9DDD5585C142');
			$this->addSql('DROP INDEX UNIQ_53BB9DDD5585C142 ON decorations');
			$this->addSql('ALTER TABLE decorations DROP skill_id');
		}

		public function down(Schema $schema): void {
			$this->throwIrreversibleMigrationException();
		}
	}
