<?php declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180324193558 extends AbstractMigration {
		public function up(Schema $schema) {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE armor_sets (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, rank VARCHAR(32) NOT NULL, UNIQUE INDEX UNIQ_7C8A0B105E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
			$this->addSql('CREATE TABLE armor_sets_armor_pieces (armor_set_id INT UNSIGNED NOT NULL, armor_id INT UNSIGNED NOT NULL, INDEX IDX_1C5D7E9D537E6F87 (armor_set_id), INDEX IDX_1C5D7E9DF5AA3663 (armor_id), PRIMARY KEY(armor_set_id, armor_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
			$this->addSql('ALTER TABLE armor_sets_armor_pieces ADD CONSTRAINT FK_1C5D7E9D537E6F87 FOREIGN KEY (armor_set_id) REFERENCES armor_sets (id)');
			$this->addSql('ALTER TABLE armor_sets_armor_pieces ADD CONSTRAINT FK_1C5D7E9DF5AA3663 FOREIGN KEY (armor_id) REFERENCES armor (id)');
		}

		public function down(Schema $schema) {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE armor_sets_armor_pieces DROP FOREIGN KEY FK_1C5D7E9D537E6F87');
			$this->addSql('DROP TABLE armor_sets');
			$this->addSql('DROP TABLE armor_sets_armor_pieces');
		}
	}
