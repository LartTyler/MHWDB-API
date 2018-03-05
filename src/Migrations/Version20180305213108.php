<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180305213108 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE armor ADD rank VARCHAR(16) NOT NULL, CHANGE attributes attributes JSON NOT NULL');
        $this->addSql('ALTER TABLE skill_ranks CHANGE modifiers modifiers JSON NOT NULL');
        $this->addSql('ALTER TABLE weapons CHANGE attributes attributes JSON NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE armor DROP rank, CHANGE attributes attributes LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE skill_ranks CHANGE modifiers modifiers LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE weapons CHANGE attributes attributes LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
