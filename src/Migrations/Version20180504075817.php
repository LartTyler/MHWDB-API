<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180504075817 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE armor_crafting_info (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE armor_crafting_material_costs (armor_id INT UNSIGNED NOT NULL, crafting_material_cost_id INT UNSIGNED NOT NULL, INDEX IDX_8EE9EA58F5AA3663 (armor_id), UNIQUE INDEX UNIQ_8EE9EA58DD94392C (crafting_material_cost_id), PRIMARY KEY(armor_id, crafting_material_cost_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE armor_crafting_material_costs ADD CONSTRAINT FK_8EE9EA58F5AA3663 FOREIGN KEY (armor_id) REFERENCES armor_crafting_info (id)');
			$this->addSql('ALTER TABLE armor_crafting_material_costs ADD CONSTRAINT FK_8EE9EA58DD94392C FOREIGN KEY (crafting_material_cost_id) REFERENCES crafting_material_costs (id)');
			$this->addSql('ALTER TABLE armor ADD crafting_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE armor ADD CONSTRAINT FK_BF27FEFC23BE98B5 FOREIGN KEY (crafting_id) REFERENCES armor_crafting_info (id)');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_BF27FEFC23BE98B5 ON armor (crafting_id)');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE armor DROP FOREIGN KEY FK_BF27FEFC23BE98B5');
			$this->addSql('ALTER TABLE armor_crafting_material_costs DROP FOREIGN KEY FK_8EE9EA58F5AA3663');
			$this->addSql('DROP TABLE armor_crafting_info');
			$this->addSql('DROP TABLE armor_crafting_material_costs');
			$this->addSql('DROP INDEX UNIQ_BF27FEFC23BE98B5 ON armor');
			$this->addSql('ALTER TABLE armor DROP crafting_id');
		}
	}
