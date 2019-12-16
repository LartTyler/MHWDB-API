<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180423145852 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE weapon_sharpnesses (id INT UNSIGNED AUTO_INCREMENT NOT NULL, red INT UNSIGNED NOT NULL, orange INT UNSIGNED NOT NULL, yellow INT UNSIGNED NOT NULL, green INT UNSIGNED NOT NULL, blue INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE weapons ADD sharpness_id INT UNSIGNED NULL');
			$this->addSql('ALTER TABLE weapons ADD CONSTRAINT FK_520EBBE1537ED785 FOREIGN KEY (sharpness_id) REFERENCES weapon_sharpnesses (id)');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_520EBBE1537ED785 ON weapons (sharpness_id)');
		}

		public function postUp(Schema $schema): void {
			$this->write('     <comment>-></comment> Adding empty sharpness entries for weapons');

			$stmt = $this->connection->createQueryBuilder()
				->from('weapons', 'w')
				->select('w.id')
				->execute();

			$weaponUpdateQuery = $this->connection->createQueryBuilder()
				->update('weapons', 'w')
				->set('w.sharpness_id', ':sharpnessId')
				->where('w.id = :weaponId')
				->getSQL();

			while ($row = $stmt->fetch(\PDO::FETCH_OBJ)) {
				$this->connection->createQueryBuilder()
					->insert('weapon_sharpnesses')
					->values([
						'red' => 0,
						'orange' => 0,
						'yellow' => 0,
						'green' => 0,
						'blue' => 0,
					])
					->execute();

				$sharpnessId = $this->connection->lastInsertId();

				/** @noinspection PhpUnhandledExceptionInspection */
				$this->connection->executeUpdate($weaponUpdateQuery, [
					'sharpnessId' => $sharpnessId,
					'weaponId' => $row->id,
				], [
					\PDO::PARAM_INT,
					\PDO::PARAM_INT,
				]);
			}

			$this->connection->exec('ALTER TABLE weapons CHANGE sharpness_id sharpness_id INT UNSIGNED NOT NULL');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE weapons DROP FOREIGN KEY FK_520EBBE1537ED785');
			$this->addSql('DROP TABLE weapon_sharpnesses');
			$this->addSql('DROP INDEX UNIQ_520EBBE1537ED785 ON weapons');
			$this->addSql('ALTER TABLE weapons DROP sharpness_id');
		}
	}
