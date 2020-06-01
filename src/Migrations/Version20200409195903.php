<?php
	declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20200409195903 extends AbstractMigration {
		public function getDescription(): string {
			return '';
		}

		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('CREATE TABLE quest_rewards (id INT UNSIGNED AUTO_INCREMENT NOT NULL, quest_id INT UNSIGNED NOT NULL, item_id INT UNSIGNED NOT NULL, INDEX IDX_BD05A37C209E9EF4 (quest_id), INDEX IDX_BD05A37C126F525E (item_id), UNIQUE INDEX UNIQ_BD05A37C209E9EF4126F525E (quest_id, item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE quest_reward_conditions (quest_reward_id INT UNSIGNED NOT NULL, reward_condition_id INT UNSIGNED NOT NULL, INDEX IDX_40FEEF82423BA179 (quest_reward_id), INDEX IDX_40FEEF82E0BFB5A3 (reward_condition_id), PRIMARY KEY(quest_reward_id, reward_condition_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('ALTER TABLE quest_rewards ADD CONSTRAINT FK_BD05A37C209E9EF4 FOREIGN KEY (quest_id) REFERENCES quests (id)');
			$this->addSql('ALTER TABLE quest_rewards ADD CONSTRAINT FK_BD05A37C126F525E FOREIGN KEY (item_id) REFERENCES items (id)');
			$this->addSql('ALTER TABLE quest_reward_conditions ADD CONSTRAINT FK_40FEEF82423BA179 FOREIGN KEY (quest_reward_id) REFERENCES quest_rewards (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE quest_reward_conditions ADD CONSTRAINT FK_40FEEF82E0BFB5A3 FOREIGN KEY (reward_condition_id) REFERENCES reward_conditions (id) ON DELETE CASCADE');
			$this->addSql('DROP TABLE world_event_strings');

			// Delete all events to prevent `null` values in the `quest_id` column
			$this->addSql('DELETE FROM world_events');

			$this->addSql('ALTER TABLE world_events DROP FOREIGN KEY FK_C3B92A0964D218E');
			$this->addSql('DROP INDEX IDX_C3B92A0964D218E ON world_events');
			$this->addSql('ALTER TABLE world_events DROP quest_rank, DROP master_rank, DROP location_id, ADD quest_id INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE world_events ADD CONSTRAINT FK_C3B92A09209E9EF4 FOREIGN KEY (quest_id) REFERENCES quests (id)');
			$this->addSql('CREATE INDEX IDX_C3B92A09209E9EF4 ON world_events (quest_id)');
		}

		public function down(Schema $schema): void {
			$this->throwIrreversibleMigrationException();
		}
	}
