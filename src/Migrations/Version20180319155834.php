<?php declare(strict_types=1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Migrations\AbstractMigration;
	use Doctrine\DBAL\Schema\Schema;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	class Version20180319155834 extends AbstractMigration {
		public function up(Schema $schema) {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('CREATE TABLE items (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, rarity SMALLINT UNSIGNED NOT NULL, sell_price INT UNSIGNED NOT NULL, buy_price INT UNSIGNED NOT NULL, carry_limit INT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_E11EE94D5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
		}

		public function down(Schema $schema) {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('DROP TABLE items');
		}
	}
