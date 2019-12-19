<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191219213144 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ailment_strings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, ailment_id INT UNSIGNED NOT NULL, language VARCHAR(7) NOT NULL, name VARCHAR(32) DEFAULT NULL, description LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_CA3B2B475E237E06 (name), INDEX IDX_CA3B2B47432CD43A (ailment_id), UNIQUE INDEX UNIQ_CA3B2B47432CD43AD4DB71B5 (ailment_id, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ailment_strings ADD CONSTRAINT FK_CA3B2B47432CD43A FOREIGN KEY (ailment_id) REFERENCES ailments (id)');
        $this->addSql('DROP INDEX UNIQ_B53777C85E237E06 ON ailments');
        $this->addSql('INSERT INTO ailment_strings (ailment_id, language, name, description) SELECT id, "en", name, description FROM ailments');
        $this->addSql('ALTER TABLE ailments DROP name, DROP description');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

		$this->addSql('ALTER TABLE ailments ADD name VARCHAR(32) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_B53777C85E237E06 ON ailments (name)');
		$this->addSql('UPDATE ailments a SET a.name = as.name, a.description = as.description FROM ailments a LEFT JOIN ailment_strings as ON a.id = as.ailment_id AND as.language = "en"');
		$this->addSql('DROP TABLE ailment_strings');
	}
}
