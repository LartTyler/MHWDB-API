<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180226215402 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE charms (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, slug VARCHAR(64) NOT NULL, UNIQUE INDEX UNIQ_5B50F9EF5E237E06 (name), UNIQUE INDEX UNIQ_5B50F9EF989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE charms_skill_ranks (charm_id INT UNSIGNED NOT NULL, skill_rank_id INT UNSIGNED NOT NULL, INDEX IDX_31EF1D4593E9261F (charm_id), INDEX IDX_31EF1D456CE3F9A6 (skill_rank_id), PRIMARY KEY(charm_id, skill_rank_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE charms_skill_ranks ADD CONSTRAINT FK_31EF1D4593E9261F FOREIGN KEY (charm_id) REFERENCES charms (id)');
        $this->addSql('ALTER TABLE charms_skill_ranks ADD CONSTRAINT FK_31EF1D456CE3F9A6 FOREIGN KEY (skill_rank_id) REFERENCES skill_ranks (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE charms_skill_ranks DROP FOREIGN KEY FK_31EF1D4593E9261F');
        $this->addSql('DROP TABLE charms');
        $this->addSql('DROP TABLE charms_skill_ranks');
    }
}
