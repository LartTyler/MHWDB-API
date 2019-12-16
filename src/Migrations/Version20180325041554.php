<?php declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180325041554 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE armor ADD armor_set_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE armor ADD CONSTRAINT FK_BF27FEFC537E6F87 FOREIGN KEY (armor_set_id) REFERENCES armor_sets (id)');
			$this->addSql('CREATE INDEX IDX_BF27FEFC537E6F87 ON armor (armor_set_id)');

			$stmt = $this->connection->createQueryBuilder()
				->from('armor_sets_armor_pieces', 'ap')
				->select('ap.armor_set_id', 'ap.armor_id')
				->execute();

			while ($row = $stmt->fetch(\PDO::FETCH_OBJ))
				$this->addSql('UPDATE armor SET armor_set_id = :setId WHERE id = :id', [
					'id' => $row->armor_id,
					'setId' => $row->armor_set_id,
				], [
					\PDO::PARAM_INT,
					\PDO::PARAM_INT,
				]);

			$this->addSql('DROP TABLE armor_sets_armor_pieces');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE armor_sets_armor_pieces (armor_set_id INT UNSIGNED NOT NULL, armor_id INT UNSIGNED NOT NULL, INDEX IDX_1C5D7E9D537E6F87 (armor_set_id), INDEX IDX_1C5D7E9DF5AA3663 (armor_id), PRIMARY KEY(armor_set_id, armor_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE armor_sets_armor_pieces ADD CONSTRAINT FK_1C5D7E9D537E6F87 FOREIGN KEY (armor_set_id) REFERENCES armor_sets (id)');
			$this->addSql('ALTER TABLE armor_sets_armor_pieces ADD CONSTRAINT FK_1C5D7E9DF5AA3663 FOREIGN KEY (armor_id) REFERENCES armor (id)');

			$stmt = $this->connection->createQueryBuilder()
				->from('armor', 'a')
				->select('a.id', 'a.armor_set_id')
				->execute();

			while ($row = $stmt->fetch(\PDO::FETCH_OBJ))
				$this->addSql('INSERT INTO armor_sets_armor_pieces (armor_set_id, armor_id) VALUES (:armorId, :setId)', [
					'armorId' => $row->id,
					'setId' => $row->armor_set_id,
				], [
					\PDO::PARAM_INT,
					\PDO::PARAM_INT,
				]);

			$this->addSql('ALTER TABLE armor DROP FOREIGN KEY FK_BF27FEFC537E6F87');
			$this->addSql('DROP INDEX IDX_BF27FEFC537E6F87 ON armor');
			$this->addSql('ALTER TABLE armor DROP armor_set_id');
		}
	}
