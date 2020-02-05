<?php
	declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20200205193214 extends AbstractMigration {
		public function getDescription(): string {
			return '';
		}

		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('CREATE TABLE charm_rank_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, charm_rank_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_3E4E813A5E237E06 (name), INDEX IDX_3E4E813A3BA5C9D1 (charm_rank_id), UNIQUE INDEX UNIQ_3E4E813A3BA5C9D1D4DB71B5 (charm_rank_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('ALTER TABLE charm_rank_strings ADD CONSTRAINT FK_3E4E813A3BA5C9D1 FOREIGN KEY (charm_rank_id) REFERENCES charm_ranks (id)');
			$this->addSql('INSERT INTO charm_rank_strings (charm_rank_id, language, name) SELECT id, "en", name FROM charm_ranks');
			$this->addSql('DROP INDEX UNIQ_DF91C6555E237E06 ON charm_ranks');
			$this->addSql('ALTER TABLE charm_ranks DROP name');
		}

		public function down(Schema $schema): void {
			$this->throwIrreversibleMigrationException();
		}
	}
