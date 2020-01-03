<?php
	declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20200103060043 extends AbstractMigration {
		public function getDescription(): string {
			return '';
		}

		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('ALTER TABLE armor DROP skills_length, DROP slots_length');
			$this->addSql('ALTER TABLE weapon_crafting_info DROP branches_length, DROP crafting_materials_length, DROP upgrade_materials_length');
			$this->addSql('ALTER TABLE charm_rank_crafting_info DROP materials_length');
			$this->addSql('ALTER TABLE weapons DROP elements_length, DROP slots_length, DROP durability_length, DROP ammo_length, DROP coatings_length');
			$this->addSql('ALTER TABLE locations DROP camps_length');
			$this->addSql('ALTER TABLE weapon_ammo DROP capacities_length');
			$this->addSql('ALTER TABLE monsters DROP ailments_length, DROP locations_length, DROP elements_length, DROP rewards_length');
			$this->addSql('ALTER TABLE charms DROP ranks_length');
			$this->addSql('ALTER TABLE decorations DROP skills_length');
			$this->addSql('ALTER TABLE motion_values DROP hits_length');
			$this->addSql('ALTER TABLE skills DROP ranks_length');
			$this->addSql('ALTER TABLE charm_ranks DROP skills_length');
			$this->addSql('ALTER TABLE armor_crafting_info DROP materials_length');
			$this->addSql('ALTER TABLE armor_set_bonuses DROP ranks_length');
			$this->addSql('ALTER TABLE armor_sets DROP pieces_length');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->throwIrreversibleMigrationException();
		}
	}
