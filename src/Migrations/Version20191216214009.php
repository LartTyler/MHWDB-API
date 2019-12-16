<?php
	declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20191216214009 extends AbstractMigration {
		public function getDescription(): string {
			return '';
		}

		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('DROP INDEX UNIQ_C3B92A093952D0CB5E237E066D1E9DF8 ON world_events');
			$this->addSql('ALTER TABLE world_events ADD expansion VARCHAR(32) NOT NULL DEFAULT "base", ADD master_rank TINYINT(1) NOT NULL DEFAULT "0"');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_C3B92A093952D0CB5E237E06F0695B726D1E9DF8 ON world_events (platform, name, expansion, start_timestamp)');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('DROP INDEX UNIQ_C3B92A093952D0CB5E237E06F0695B726D1E9DF8 ON world_events');
			$this->addSql('ALTER TABLE world_events DROP expansion');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_C3B92A093952D0CB5E237E066D1E9DF8 ON world_events (platform, name, start_timestamp)');
		}
	}
