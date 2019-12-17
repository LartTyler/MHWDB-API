<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180424202745 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE decorations_skill_ranks (decoration_id INT UNSIGNED NOT NULL, skill_rank_id INT UNSIGNED NOT NULL, INDEX IDX_5D2A04FA3446DFC4 (decoration_id), INDEX IDX_5D2A04FA6CE3F9A6 (skill_rank_id), PRIMARY KEY(decoration_id, skill_rank_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE decorations_skill_ranks ADD CONSTRAINT FK_5D2A04FA3446DFC4 FOREIGN KEY (decoration_id) REFERENCES decorations (id)');
			$this->addSql('ALTER TABLE decorations_skill_ranks ADD CONSTRAINT FK_5D2A04FA6CE3F9A6 FOREIGN KEY (skill_rank_id) REFERENCES skill_ranks (id)');

			$stmt = $this->connection->createQueryBuilder()
				->from('decorations', 'd')
				->select('d.id', 'd.skill_id')
				->execute();

			while ($deco = $stmt->fetch(\PDO::FETCH_OBJ))
				$this->addSql('INSERT INTO decorations_skill_ranks (decoration_id, skill_rank_id) VALUES (?, ?)', [
					(int)$deco->id,
					(int)$deco->skill_id,
				], [
					\PDO::PARAM_INT,
					\PDO::PARAM_INT,
				]);
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('DROP TABLE decorations_skill_ranks');
		}
	}
