<?php declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180311184548 extends AbstractMigration {
		public function up(Schema $schema) {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE weapon_upgrade_nodes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, weapon_id INT UNSIGNED NOT NULL, previous_id INT UNSIGNED DEFAULT NULL, craftable TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_9933817895B82273 (weapon_id), INDEX IDX_993381782DE62210 (previous_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE weapon_upgrade_nodes_branches (node_id INT UNSIGNED NOT NULL, branch_id INT UNSIGNED NOT NULL, INDEX IDX_8EA888C2460D9FD7 (node_id), INDEX IDX_8EA888C2DCD6CC49 (branch_id), PRIMARY KEY(node_id, branch_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE weapon_upgrade_nodes ADD CONSTRAINT FK_9933817895B82273 FOREIGN KEY (weapon_id) REFERENCES weapons (id)');
			$this->addSql('ALTER TABLE weapon_upgrade_nodes ADD CONSTRAINT FK_993381782DE62210 FOREIGN KEY (previous_id) REFERENCES weapon_upgrade_nodes (id)');
			$this->addSql('ALTER TABLE weapon_upgrade_nodes_branches ADD CONSTRAINT FK_8EA888C2460D9FD7 FOREIGN KEY (node_id) REFERENCES weapon_upgrade_nodes (id)');
			$this->addSql('ALTER TABLE weapon_upgrade_nodes_branches ADD CONSTRAINT FK_8EA888C2DCD6CC49 FOREIGN KEY (branch_id) REFERENCES weapon_upgrade_nodes (id)');
		}

		public function down(Schema $schema) {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE weapon_upgrade_nodes DROP FOREIGN KEY FK_993381782DE62210');
			$this->addSql('ALTER TABLE weapon_upgrade_nodes_branches DROP FOREIGN KEY FK_8EA888C2460D9FD7');
			$this->addSql('ALTER TABLE weapon_upgrade_nodes_branches DROP FOREIGN KEY FK_8EA888C2DCD6CC49');
			$this->addSql('DROP TABLE weapon_upgrade_nodes');
			$this->addSql('DROP TABLE weapon_upgrade_nodes_branches');
		}
	}
