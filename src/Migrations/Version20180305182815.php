<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180305182815 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE armor (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, slug VARCHAR(64) NOT NULL, type VARCHAR(32) NOT NULL, attributes JSON NOT NULL, UNIQUE INDEX UNIQ_BF27FEFC5E237E06 (name), UNIQUE INDEX UNIQ_BF27FEFC989D9B62 (slug), INDEX type_idx (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE armor_skill_ranks (armor_id INT UNSIGNED NOT NULL, skill_rank_id INT UNSIGNED NOT NULL, INDEX IDX_101D79CCF5AA3663 (armor_id), INDEX IDX_101D79CC6CE3F9A6 (skill_rank_id), PRIMARY KEY(armor_id, skill_rank_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE charms (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, slug VARCHAR(64) NOT NULL, UNIQUE INDEX UNIQ_5B50F9EF5E237E06 (name), UNIQUE INDEX UNIQ_5B50F9EF989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE charms_skill_ranks (charm_id INT UNSIGNED NOT NULL, skill_rank_id INT UNSIGNED NOT NULL, INDEX IDX_31EF1D4593E9261F (charm_id), INDEX IDX_31EF1D456CE3F9A6 (skill_rank_id), PRIMARY KEY(charm_id, skill_rank_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skills (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, slug VARCHAR(64) NOT NULL, UNIQUE INDEX UNIQ_D53116705E237E06 (name), UNIQUE INDEX UNIQ_D5311670989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skill_ranks (id INT UNSIGNED AUTO_INCREMENT NOT NULL, skill_id INT UNSIGNED NOT NULL, level SMALLINT UNSIGNED NOT NULL, description LONGTEXT NOT NULL, modifiers JSON NOT NULL, slug VARCHAR(64) NOT NULL, UNIQUE INDEX UNIQ_EECE3328989D9B62 (slug), INDEX IDX_EECE33285585C142 (skill_id), UNIQUE INDEX skill_level_idx (skill_id, level), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE weapons (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, type VARCHAR(32) NOT NULL, rarity SMALLINT UNSIGNED NOT NULL, slug VARCHAR(64) NOT NULL, attributes JSON NOT NULL, UNIQUE INDEX UNIQ_520EBBE15E237E06 (name), UNIQUE INDEX UNIQ_520EBBE1989D9B62 (slug), INDEX type_idx (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE armor_skill_ranks ADD CONSTRAINT FK_101D79CCF5AA3663 FOREIGN KEY (armor_id) REFERENCES armor (id)');
        $this->addSql('ALTER TABLE armor_skill_ranks ADD CONSTRAINT FK_101D79CC6CE3F9A6 FOREIGN KEY (skill_rank_id) REFERENCES skill_ranks (id)');
        $this->addSql('ALTER TABLE charms_skill_ranks ADD CONSTRAINT FK_31EF1D4593E9261F FOREIGN KEY (charm_id) REFERENCES charms (id)');
        $this->addSql('ALTER TABLE charms_skill_ranks ADD CONSTRAINT FK_31EF1D456CE3F9A6 FOREIGN KEY (skill_rank_id) REFERENCES skill_ranks (id)');
        $this->addSql('ALTER TABLE skill_ranks ADD CONSTRAINT FK_EECE33285585C142 FOREIGN KEY (skill_id) REFERENCES skills (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE armor_skill_ranks DROP FOREIGN KEY FK_101D79CCF5AA3663');
        $this->addSql('ALTER TABLE charms_skill_ranks DROP FOREIGN KEY FK_31EF1D4593E9261F');
        $this->addSql('ALTER TABLE skill_ranks DROP FOREIGN KEY FK_EECE33285585C142');
        $this->addSql('ALTER TABLE armor_skill_ranks DROP FOREIGN KEY FK_101D79CC6CE3F9A6');
        $this->addSql('ALTER TABLE charms_skill_ranks DROP FOREIGN KEY FK_31EF1D456CE3F9A6');
        $this->addSql('DROP TABLE armor');
        $this->addSql('DROP TABLE armor_skill_ranks');
        $this->addSql('DROP TABLE charms');
        $this->addSql('DROP TABLE charms_skill_ranks');
        $this->addSql('DROP TABLE skills');
        $this->addSql('DROP TABLE skill_ranks');
        $this->addSql('DROP TABLE weapons');
    }
}
