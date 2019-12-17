<?php declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180323034352 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE weapon_crafting_info_upgrade_material_costs (weapon_crafting_info_id INT UNSIGNED NOT NULL, crafting_material_cost_id INT UNSIGNED NOT NULL, INDEX IDX_B0AB8CFDD4C2CCD2 (weapon_crafting_info_id), INDEX IDX_B0AB8CFDDD94392C (crafting_material_cost_id), PRIMARY KEY(weapon_crafting_info_id, crafting_material_cost_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE weapon_crafting_info_upgrade_material_costs ADD CONSTRAINT FK_B0AB8CFDD4C2CCD2 FOREIGN KEY (weapon_crafting_info_id) REFERENCES weapon_crafting_info (id)');
			$this->addSql('ALTER TABLE weapon_crafting_info_upgrade_material_costs ADD CONSTRAINT FK_B0AB8CFDDD94392C FOREIGN KEY (crafting_material_cost_id) REFERENCES crafting_material_costs (id)');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('DROP TABLE weapon_crafting_info_upgrade_material_costs');
		}
	}
