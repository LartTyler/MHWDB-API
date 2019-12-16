<?php declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180314175357 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE weapon_crafting_info (id INT UNSIGNED AUTO_INCREMENT NOT NULL, previous_id INT UNSIGNED DEFAULT NULL, craftable TINYINT(1) NOT NULL, INDEX IDX_BBF70A542DE62210 (previous_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE weapon_crafting_info_branches (weapon_crafting_info_id INT UNSIGNED NOT NULL, branch_id INT UNSIGNED NOT NULL, INDEX IDX_960A7F5CD4C2CCD2 (weapon_crafting_info_id), INDEX IDX_960A7F5CDCD6CC49 (branch_id), PRIMARY KEY(weapon_crafting_info_id, branch_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE weapon_crafting_info ADD CONSTRAINT FK_BBF70A542DE62210 FOREIGN KEY (previous_id) REFERENCES weapons (id)');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches ADD CONSTRAINT FK_960A7F5CD4C2CCD2 FOREIGN KEY (weapon_crafting_info_id) REFERENCES weapon_crafting_info (id)');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches ADD CONSTRAINT FK_960A7F5CDCD6CC49 FOREIGN KEY (branch_id) REFERENCES weapons (id)');
			$this->addSql('ALTER TABLE weapons ADD crafting_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapons ADD CONSTRAINT FK_520EBBE123BE98B5 FOREIGN KEY (crafting_id) REFERENCES weapon_crafting_info (id)');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_520EBBE123BE98B5 ON weapons (crafting_id)');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE weapons DROP FOREIGN KEY FK_520EBBE123BE98B5');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches DROP FOREIGN KEY FK_960A7F5CD4C2CCD2');
			$this->addSql('DROP TABLE weapon_crafting_info');
			$this->addSql('DROP TABLE weapon_crafting_info_branches');
			$this->addSql('DROP INDEX UNIQ_520EBBE123BE98B5 ON weapons');
			$this->addSql('ALTER TABLE weapons DROP crafting_id');
		}
	}
