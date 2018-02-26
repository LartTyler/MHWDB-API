<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use App\Utility\StringUtil;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180226192228 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE skills ADD slug VARCHAR(64) NOT NULL');

        $statement = $this->connection->createQueryBuilder()
			->from('skills', 's')
			->select('s.id', 's.name')
			->execute();

        while ($row = $statement->fetch(\PDO::FETCH_OBJ))
			$this->addSql('UPDATE skills SET slug = :slug WHERE id = :id', [
				'slug' => StringUtil::toSlug($row->name),
				'id' => $row->id,
			]);

        $this->addSql('CREATE UNIQUE INDEX UNIQ_D5311670989D9B62 ON skills (slug)');
        $this->addSql('ALTER TABLE skill_ranks ADD slug VARCHAR(64) NOT NULL');

        $statement = $this->connection->createQueryBuilder()
			->from('skill_ranks', 'r')
			->leftJoin('r', 'skills', 's', 'r.skill_id = s.id')
			->select('r.id', 's.name AS skill_name', 'r.level')
			->execute();

        while ($row = $statement->fetch(\PDO::FETCH_OBJ))
			$this->addSql('UPDATE skill_ranks SET slug = :slug WHERE id = :id', [
				'slug' => StringUtil::toSlug($row->skill_name . '-rank-' . $row->level),
				'id' => $row->id,
			]);

        $this->addSql('CREATE UNIQUE INDEX UNIQ_EECE3328989D9B62 ON skill_ranks (slug)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_EECE3328989D9B62 ON skill_ranks');
        $this->addSql('ALTER TABLE skill_ranks DROP slug');
        $this->addSql('DROP INDEX UNIQ_D5311670989D9B62 ON skills');
        $this->addSql('ALTER TABLE skills DROP slug');
    }
}
