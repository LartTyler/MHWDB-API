<?php
	declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20200213222709 extends AbstractMigration {
		public function getDescription(): string {
			return '';
		}

		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('CREATE TABLE quests (id INT UNSIGNED AUTO_INCREMENT NOT NULL, location_id INT UNSIGNED NOT NULL, item_id INT UNSIGNED DEFAULT NULL, objective VARCHAR(18) NOT NULL, rank VARCHAR(6) NOT NULL, stars SMALLINT UNSIGNED NOT NULL, time_limit SMALLINT UNSIGNED NOT NULL, max_hunters SMALLINT UNSIGNED NOT NULL, max_faints SMALLINT UNSIGNED NOT NULL, subject VARCHAR(7) NOT NULL, amount SMALLINT UNSIGNED DEFAULT NULL, INDEX IDX_989E5D3464D218E (location_id), INDEX IDX_989E5D34126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE quest_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, quest_id INT UNSIGNED NOT NULL, name VARCHAR(128) NOT NULL, description LONGTEXT NOT NULL, object_name VARCHAR(64) DEFAULT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_E0BDD7BE5E237E06 (name), INDEX IDX_E0BDD7BE209E9EF4 (quest_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE endemic_life_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, endemic_life_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT DEFAULT NULL, language VARCHAR(7) NOT NULL, INDEX IDX_2CBF2B1C20AAC085 (endemic_life_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE endemic_life (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(12) NOT NULL, research_point_value SMALLINT UNSIGNED NOT NULL, spawn_conditions VARCHAR(5) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE endemic_life_locations (endemic_life_id INT UNSIGNED NOT NULL, location_id INT UNSIGNED NOT NULL, INDEX IDX_9981EB3C20AAC085 (endemic_life_id), UNIQUE INDEX UNIQ_9981EB3C64D218E (location_id), PRIMARY KEY(endemic_life_id, location_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE quest_delivery_targets (id INT UNSIGNED AUTO_INCREMENT NOT NULL, quest_id INT UNSIGNED NOT NULL, endemic_life_id INT UNSIGNED DEFAULT NULL, deliveryType VARCHAR(12) NOT NULL, INDEX IDX_296CB292209E9EF4 (quest_id), INDEX IDX_296CB29220AAC085 (endemic_life_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('CREATE TABLE quest_monster_targets (id INT UNSIGNED AUTO_INCREMENT NOT NULL, quest_id INT UNSIGNED NOT NULL, monster_id INT UNSIGNED NOT NULL, amount SMALLINT UNSIGNED NOT NULL, INDEX IDX_EF4D0372209E9EF4 (quest_id), INDEX IDX_EF4D0372C5FF1223 (monster_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
			$this->addSql('ALTER TABLE quests ADD CONSTRAINT FK_989E5D3464D218E FOREIGN KEY (location_id) REFERENCES locations (id)');
			$this->addSql('ALTER TABLE quests ADD CONSTRAINT FK_989E5D34126F525E FOREIGN KEY (item_id) REFERENCES items (id)');
			$this->addSql('ALTER TABLE quest_strings ADD CONSTRAINT FK_E0BDD7BE209E9EF4 FOREIGN KEY (quest_id) REFERENCES quests (id)');
			$this->addSql('ALTER TABLE endemic_life_strings ADD CONSTRAINT FK_2CBF2B1C20AAC085 FOREIGN KEY (endemic_life_id) REFERENCES endemic_life (id)');
			$this->addSql('ALTER TABLE endemic_life_locations ADD CONSTRAINT FK_9981EB3C20AAC085 FOREIGN KEY (endemic_life_id) REFERENCES endemic_life (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE endemic_life_locations ADD CONSTRAINT FK_9981EB3C64D218E FOREIGN KEY (location_id) REFERENCES locations (id)');
			$this->addSql('ALTER TABLE quest_delivery_targets ADD CONSTRAINT FK_296CB292209E9EF4 FOREIGN KEY (quest_id) REFERENCES quests (id)');
			$this->addSql('ALTER TABLE quest_delivery_targets ADD CONSTRAINT FK_296CB29220AAC085 FOREIGN KEY (endemic_life_id) REFERENCES endemic_life (id)');
			$this->addSql('ALTER TABLE quest_monster_targets ADD CONSTRAINT FK_EF4D0372209E9EF4 FOREIGN KEY (quest_id) REFERENCES quests (id)');
			$this->addSql('ALTER TABLE quest_monster_targets ADD CONSTRAINT FK_EF4D0372C5FF1223 FOREIGN KEY (monster_id) REFERENCES monsters (id)');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('ALTER TABLE quest_strings DROP FOREIGN KEY FK_E0BDD7BE209E9EF4');
			$this->addSql('ALTER TABLE quest_delivery_targets DROP FOREIGN KEY FK_296CB292209E9EF4');
			$this->addSql('ALTER TABLE quest_monster_targets DROP FOREIGN KEY FK_EF4D0372209E9EF4');
			$this->addSql('ALTER TABLE endemic_life_strings DROP FOREIGN KEY FK_2CBF2B1C20AAC085');
			$this->addSql('ALTER TABLE endemic_life_locations DROP FOREIGN KEY FK_9981EB3C20AAC085');
			$this->addSql('ALTER TABLE quest_delivery_targets DROP FOREIGN KEY FK_296CB29220AAC085');
			$this->addSql('DROP TABLE quests');
			$this->addSql('DROP TABLE quest_strings');
			$this->addSql('DROP TABLE endemic_life_strings');
			$this->addSql('DROP TABLE endemic_life');
			$this->addSql('DROP TABLE endemic_life_locations');
			$this->addSql('DROP TABLE quest_delivery_targets');
			$this->addSql('DROP TABLE quest_monster_targets');
		}
	}
