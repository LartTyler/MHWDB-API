<?php declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180320200544 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE crafting_material_costs (id INT UNSIGNED AUTO_INCREMENT NOT NULL, item_id INT UNSIGNED NOT NULL, quantity INT UNSIGNED NOT NULL, INDEX IDX_1B8FBFE6126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE weapon_crafting_info_crafting_material_costs (weapon_crafting_info_id INT UNSIGNED NOT NULL, crafting_material_cost_id INT UNSIGNED NOT NULL, INDEX IDX_DCB3F113D4C2CCD2 (weapon_crafting_info_id), INDEX IDX_DCB3F113DD94392C (crafting_material_cost_id), PRIMARY KEY(weapon_crafting_info_id, crafting_material_cost_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE crafting_material_costs ADD CONSTRAINT FK_1B8FBFE6126F525E FOREIGN KEY (item_id) REFERENCES items (id)');
			$this->addSql('ALTER TABLE weapon_crafting_info_crafting_material_costs ADD CONSTRAINT FK_DCB3F113D4C2CCD2 FOREIGN KEY (weapon_crafting_info_id) REFERENCES weapon_crafting_info (id)');
			$this->addSql('ALTER TABLE weapon_crafting_info_crafting_material_costs ADD CONSTRAINT FK_DCB3F113DD94392C FOREIGN KEY (crafting_material_cost_id) REFERENCES crafting_material_costs (id)');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE weapon_crafting_info_crafting_material_costs DROP FOREIGN KEY FK_DCB3F113DD94392C');
			$this->addSql('DROP TABLE crafting_material_costs');
			$this->addSql('DROP TABLE weapon_crafting_info_crafting_material_costs');
		}
	}
