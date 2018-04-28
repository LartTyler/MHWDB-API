<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180425224813 extends AbstractMigration {
		public function up(Schema $schema) {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE charm_rank_crafting_info (id INT UNSIGNED AUTO_INCREMENT NOT NULL, craftable TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE charm_rank_crafting_info_crafting_material_costs (charm_rank_id INT UNSIGNED NOT NULL, crafting_material_cost_id INT UNSIGNED NOT NULL, INDEX IDX_8EEF4252DD94392C (crafting_material_cost_id), PRIMARY KEY(charm_rank_id, crafting_material_cost_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs ADD CONSTRAINT FK_8EEF42523BA5C9D1 FOREIGN KEY (charm_rank_id) REFERENCES charm_rank_crafting_info (id)');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs ADD CONSTRAINT FK_8EEF4252DD94392C FOREIGN KEY (crafting_material_cost_id) REFERENCES crafting_material_costs (id)');
			$this->addSql('ALTER TABLE charm_ranks ADD crafting_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE charm_ranks ADD CONSTRAINT FK_DF91C65523BE98B5 FOREIGN KEY (crafting_id) REFERENCES charm_rank_crafting_info (id)');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_DF91C65523BE98B5 ON charm_ranks (crafting_id)');
		}

		public function down(Schema $schema) {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE charm_ranks DROP FOREIGN KEY FK_DF91C65523BE98B5');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs DROP FOREIGN KEY FK_8EEF42523BA5C9D1');
			$this->addSql('DROP TABLE charm_rank_crafting_info');
			$this->addSql('DROP TABLE charm_rank_crafting_info_crafting_material_costs');
			$this->addSql('DROP INDEX UNIQ_DF91C65523BE98B5 ON charm_ranks');
			$this->addSql('ALTER TABLE charm_ranks DROP crafting_id');
		}
	}
