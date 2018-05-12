<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180509191153 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE weapons ADD attack_display SMALLINT UNSIGNED NOT NULL DEFAULT 0, ADD attack_raw SMALLINT UNSIGNED NOT NULL DEFAULT 0');
			$this->addSql('UPDATE weapons SET attack_raw = 0');
			$this->addSql('UPDATE weapons SET attack_display = JSON_UNQUOTE(JSON_EXTRACT(attributes, "$.attack"))');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE weapons DROP attack_display, DROP attack_raw');
		}
	}
