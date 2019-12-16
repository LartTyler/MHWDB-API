<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180425142735 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE charm_ranks (id INT UNSIGNED AUTO_INCREMENT NOT NULL, charm_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, level SMALLINT NOT NULL, INDEX IDX_DF91C65593E9261F (charm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE charm_ranks_skill_ranks (charm_rank_id INT UNSIGNED NOT NULL, skill_rank_id INT UNSIGNED NOT NULL, INDEX IDX_B86027C63BA5C9D1 (charm_rank_id), INDEX IDX_B86027C66CE3F9A6 (skill_rank_id), PRIMARY KEY(charm_rank_id, skill_rank_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE charm_ranks ADD CONSTRAINT FK_DF91C65593E9261F FOREIGN KEY (charm_id) REFERENCES charms (id)');
			$this->addSql('ALTER TABLE charm_ranks_skill_ranks ADD CONSTRAINT FK_B86027C63BA5C9D1 FOREIGN KEY (charm_rank_id) REFERENCES charm_ranks (id)');
			$this->addSql('ALTER TABLE charm_ranks_skill_ranks ADD CONSTRAINT FK_B86027C66CE3F9A6 FOREIGN KEY (skill_rank_id) REFERENCES skill_ranks (id)');
			$this->addSql('DROP TABLE charms_skill_ranks');

			$this->addSql('DELETE FROM charms');
		}

		public function down(Schema $schema): void {
			/** @noinspection PhpUnhandledExceptionInspection */
			$this->throwIrreversibleMigrationException();
		}
	}
