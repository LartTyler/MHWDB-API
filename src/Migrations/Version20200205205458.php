<?php
	declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20200205205458 extends AbstractMigration {
		public function getDescription(): string {
			return '';
		}

		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('ALTER TABLE reward_conditions ADD subtype VARCHAR(128) DEFAULT NULL');
			$this->addSql('UPDATE reward_conditions r LEFT JOIN reward_condition_strings s ON r.id = s.reward_condition_id AND s.language = "en" SET r.subtype = s.subtype');
			$this->addSql('DROP TABLE reward_condition_strings');
		}

		public function down(Schema $schema): void {
			$this->throwIrreversibleMigrationException();
		}
	}
