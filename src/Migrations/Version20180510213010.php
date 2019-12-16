<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180510213010 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE armor ADD skills_length INT UNSIGNED DEFAULT 0 NOT NULL, ADD slots_length INT UNSIGNED DEFAULT 0 NOT NULL');
			$this->addSql('ALTER TABLE armor_crafting_info ADD materials_length INT UNSIGNED DEFAULT 0 NOT NULL');
			$this->addSql('ALTER TABLE armor_sets ADD pieces_length INT UNSIGNED DEFAULT 0 NOT NULL');
			$this->addSql('ALTER TABLE armor_set_bonuses ADD ranks_length INT UNSIGNED DEFAULT 0 NOT NULL');
			$this->addSql('ALTER TABLE charms ADD ranks_length INT UNSIGNED DEFAULT 0 NOT NULL');
			$this->addSql('ALTER TABLE charm_ranks ADD skills_length INT UNSIGNED DEFAULT 0 NOT NULL');
			$this->addSql('ALTER TABLE charm_rank_crafting_info ADD materials_length INT UNSIGNED DEFAULT 0 NOT NULL');
			$this->addSql('ALTER TABLE decorations ADD skills_length INT UNSIGNED DEFAULT 0 NOT NULL');
			$this->addSql('ALTER TABLE skills ADD ranks_length INT UNSIGNED DEFAULT 0 NOT NULL');
			$this->addSql('ALTER TABLE skill_ranks CHANGE modifiers modifiers JSON NOT NULL');
			$this->addSql('ALTER TABLE weapons ADD elements_length INT UNSIGNED DEFAULT 0 NOT NULL, ADD slots_length INT UNSIGNED DEFAULT 0 NOT NULL');
			$this->addSql('ALTER TABLE weapon_crafting_info ADD branches_length INT UNSIGNED DEFAULT 0 NOT NULL, ADD crafting_materials_length INT UNSIGNED DEFAULT 0 NOT NULL, ADD upgrade_materials_length INT UNSIGNED DEFAULT 0 NOT NULL');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE armor DROP skills_length, DROP slots_length');
			$this->addSql('ALTER TABLE armor_crafting_info DROP materials_length');
			$this->addSql('ALTER TABLE armor_set_bonuses DROP ranks_length');
			$this->addSql('ALTER TABLE armor_sets DROP pieces_length');
			$this->addSql('ALTER TABLE charm_rank_crafting_info DROP materials_length');
			$this->addSql('ALTER TABLE charm_ranks DROP skills_length');
			$this->addSql('ALTER TABLE charms DROP ranks_length');
			$this->addSql('ALTER TABLE decorations DROP skills_length');
			$this->addSql('ALTER TABLE skills DROP ranks_length');
			$this->addSql('ALTER TABLE weapon_crafting_info DROP branches_length, DROP crafting_materials_length, DROP upgrade_materials_length');
			$this->addSql('ALTER TABLE weapons DROP elements_length, DROP slots_length');
		}
	}
