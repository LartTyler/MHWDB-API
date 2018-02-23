<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180223212626 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE skill_ranks (id INT UNSIGNED AUTO_INCREMENT NOT NULL, skill_id INT UNSIGNED NOT NULL, level SMALLINT UNSIGNED NOT NULL, description LONGTEXT NOT NULL, modifiers LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_EECE33285585C142 (skill_id), UNIQUE INDEX skill_level_idx (skill_id, level), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE skill_ranks ADD CONSTRAINT FK_EECE33285585C142 FOREIGN KEY (skill_id) REFERENCES skills (id)');
        $this->addSql('ALTER TABLE skills DROP ranks');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE skill_ranks');
        $this->addSql('ALTER TABLE skills ADD ranks LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json)\'');
    }
}
