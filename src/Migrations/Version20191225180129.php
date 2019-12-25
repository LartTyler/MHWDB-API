<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191225180129 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE armor_set_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, armor_set_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, language VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_CDE5C6F65E237E06 (name), INDEX IDX_CDE5C6F6537E6F87 (armor_set_id), UNIQUE INDEX UNIQ_CDE5C6F6537E6F87D4DB71B5 (armor_set_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE armor_set_strings ADD CONSTRAINT FK_CDE5C6F6537E6F87 FOREIGN KEY (armor_set_id) REFERENCES armor_sets (id)');
        $this->addSql('DROP INDEX UNIQ_7C8A0B105E237E06 ON armor_sets');
        $this->addSql('INSERT INTO armor_set_strings (armor_set_id, language, name) SELECT id, "en", name FROM armor_sets');
        $this->addSql('ALTER TABLE armor_sets DROP name');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

		$this->addSql('ALTER TABLE armor_sets ADD name VARCHAR(64) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_7C8A0B105E237E06 ON armor_sets (name)');
		$this->addSql('UPDATE armor_sets a SET a.name = as.name FROM armor_sets a LEFT JOIN armor_set_strings AS as ON a.id = as.armor_set_id AND as.language = "en"');
		$this->addSql('DROP TABLE armor_set_strings');
	}
}
