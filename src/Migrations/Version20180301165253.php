<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180301165253 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE armor (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, slug VARCHAR(64) NOT NULL, type VARCHAR(32) NOT NULL, attributes JSON NOT NULL, UNIQUE INDEX UNIQ_BF27FEFC5E237E06 (name), UNIQUE INDEX UNIQ_BF27FEFC989D9B62 (slug), INDEX type_idx (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE armor_skill_ranks (armor_id INT UNSIGNED NOT NULL, skill_rank_id INT UNSIGNED NOT NULL, INDEX IDX_101D79CCF5AA3663 (armor_id), INDEX IDX_101D79CC6CE3F9A6 (skill_rank_id), PRIMARY KEY(armor_id, skill_rank_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE armor_skill_ranks ADD CONSTRAINT FK_101D79CCF5AA3663 FOREIGN KEY (armor_id) REFERENCES armor (id)');
        $this->addSql('ALTER TABLE armor_skill_ranks ADD CONSTRAINT FK_101D79CC6CE3F9A6 FOREIGN KEY (skill_rank_id) REFERENCES skill_ranks (id)');
        $this->addSql('ALTER TABLE skill_ranks CHANGE modifiers modifiers JSON NOT NULL');
        $this->addSql('ALTER TABLE weapons CHANGE attributes attributes JSON NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE armor_skill_ranks DROP FOREIGN KEY FK_101D79CCF5AA3663');
        $this->addSql('DROP TABLE armor');
        $this->addSql('DROP TABLE armor_skill_ranks');
        $this->addSql('ALTER TABLE skill_ranks CHANGE modifiers modifiers LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE weapons CHANGE attributes attributes LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
