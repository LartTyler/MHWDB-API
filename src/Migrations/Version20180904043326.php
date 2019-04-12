<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180904043326 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE monster_weaknesses (id INT UNSIGNED AUTO_INCREMENT NOT NULL, monster_id INT UNSIGNED NOT NULL, element VARCHAR(32) NOT NULL, stars SMALLINT UNSIGNED NOT NULL, INDEX IDX_1D01676C5FF1223 (monster_id), UNIQUE INDEX UNIQ_1D01676C5FF122341405E39 (monster_id, element), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE monster_weaknesses ADD CONSTRAINT FK_1D01676C5FF1223 FOREIGN KEY (monster_id) REFERENCES monsters (id)');
			$this->addSql('ALTER TABLE monster_resistances ADD CONSTRAINT FK_7DE8A429C5FF1223 FOREIGN KEY (monster_id) REFERENCES monsters (id)');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_7DE8A429C5FF122341405E39 ON monster_resistances (monster_id, element)');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('DROP TABLE monster_weaknesses');
			$this->addSql('ALTER TABLE monster_resistances DROP FOREIGN KEY FK_7DE8A429C5FF1223');
			$this->addSql('DROP INDEX UNIQ_7DE8A429C5FF122341405E39 ON monster_resistances');
		}
	}
