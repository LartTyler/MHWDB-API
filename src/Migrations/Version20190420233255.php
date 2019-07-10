<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20190420233255 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('CREATE TABLE monster_rewards (id INT UNSIGNED AUTO_INCREMENT NOT NULL, monster_id INT UNSIGNED NOT NULL, item_id INT UNSIGNED NOT NULL, INDEX IDX_4B43AF08C5FF1223 (monster_id), INDEX IDX_4B43AF08126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE monster_reward_conditions (monster_reward_id INT UNSIGNED NOT NULL, reward_condition_id INT UNSIGNED NOT NULL, INDEX IDX_B134FDFDDEA7E6 (monster_reward_id), INDEX IDX_B134FDFDE0BFB5A3 (reward_condition_id), PRIMARY KEY(monster_reward_id, reward_condition_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE reward_conditions (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(32) NOT NULL, rank VARCHAR(16) NOT NULL, stack_size SMALLINT UNSIGNED NOT NULL, chance SMALLINT UNSIGNED NOT NULL, subtype VARCHAR(128) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE monster_rewards ADD CONSTRAINT FK_4B43AF08C5FF1223 FOREIGN KEY (monster_id) REFERENCES monsters (id)');
			$this->addSql('ALTER TABLE monster_rewards ADD CONSTRAINT FK_4B43AF08126F525E FOREIGN KEY (item_id) REFERENCES items (id)');
			$this->addSql('ALTER TABLE monster_reward_conditions ADD CONSTRAINT FK_B134FDFDDEA7E6 FOREIGN KEY (monster_reward_id) REFERENCES monster_rewards (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE monster_reward_conditions ADD CONSTRAINT FK_B134FDFDE0BFB5A3 FOREIGN KEY (reward_condition_id) REFERENCES reward_conditions (id) ON DELETE CASCADE');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('ALTER TABLE monster_reward_conditions DROP FOREIGN KEY FK_B134FDFDDEA7E6');
			$this->addSql('ALTER TABLE monster_reward_conditions DROP FOREIGN KEY FK_B134FDFDE0BFB5A3');
			$this->addSql('DROP TABLE monster_rewards');
			$this->addSql('DROP TABLE monster_reward_conditions');
			$this->addSql('DROP TABLE reward_conditions');
		}
	}
