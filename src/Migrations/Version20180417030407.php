<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180417030407 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE armor_set_bonuses (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE armor_set_bonus_ranks (id INT UNSIGNED AUTO_INCREMENT NOT NULL, bonus_id INT UNSIGNED NOT NULL, skill_id INT UNSIGNED NOT NULL, pieces SMALLINT UNSIGNED NOT NULL, INDEX IDX_5DF784FC69545666 (bonus_id), INDEX IDX_5DF784FC5585C142 (skill_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE armor_set_bonus_ranks ADD CONSTRAINT FK_5DF784FC69545666 FOREIGN KEY (bonus_id) REFERENCES armor_set_bonuses (id)');
			$this->addSql('ALTER TABLE armor_set_bonus_ranks ADD CONSTRAINT FK_5DF784FC5585C142 FOREIGN KEY (skill_id) REFERENCES skill_ranks (id)');
			$this->addSql('ALTER TABLE armor_sets ADD bonus_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE armor_sets ADD CONSTRAINT FK_7C8A0B1069545666 FOREIGN KEY (bonus_id) REFERENCES armor_set_bonuses (id)');
			$this->addSql('CREATE INDEX IDX_7C8A0B1069545666 ON armor_sets (bonus_id)');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE armor_sets DROP FOREIGN KEY FK_7C8A0B1069545666');
			$this->addSql('ALTER TABLE armor_set_bonus_ranks DROP FOREIGN KEY FK_5DF784FC69545666');
			$this->addSql('DROP TABLE armor_set_bonuses');
			$this->addSql('DROP TABLE armor_set_bonus_ranks');
			$this->addSql('DROP INDEX IDX_7C8A0B1069545666 ON armor_sets');
			$this->addSql('ALTER TABLE armor_sets DROP bonus_id');
		}
	}
