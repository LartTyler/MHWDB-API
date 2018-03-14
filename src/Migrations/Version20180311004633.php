<?php declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180311004633 extends AbstractMigration {
		public function up(Schema $schema) {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE decorations (id INT UNSIGNED AUTO_INCREMENT NOT NULL, skill_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, slug VARCHAR(64) NOT NULL, rarity SMALLINT UNSIGNED NOT NULL, slot SMALLINT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_53BB9DDD5E237E06 (name), UNIQUE INDEX UNIQ_53BB9DDD989D9B62 (slug), UNIQUE INDEX UNIQ_53BB9DDD5585C142 (skill_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE decorations ADD CONSTRAINT FK_53BB9DDD5585C142 FOREIGN KEY (skill_id) REFERENCES skills (id)');
		}

		public function down(Schema $schema) {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('DROP TABLE decorations');
		}
	}
