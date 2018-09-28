<?php declare(strict_types=1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180927210739 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE monster_weaknesses ADD _condition LONGTEXT DEFAULT NULL');
			$this->addSql('UPDATE monster_weaknesses SET _condition = `condition`');
			$this->addSql('ALTER TABLE monster_weaknesses DROP `condition`');
			$this->addSql('ALTER TABLE monster_resistances ADD _condition LONGTEXT DEFAULT NULL');
			$this->addSql('UPDATE monster_resistances SET _condition = `condition`');
			$this->addSql('ALTER TABLE monster_resistances DROP `condition`');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE monster_resistances ADD `condition` LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci');
			$this->addSql('UPDATE monster_resistances SET `condition` = _condition');
			$this->addSql('ALTER TABLE monster_resistances DROP _condition');
			$this->addSql('ALTER TABLE monster_weaknesses ADD `condition` LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci');
			$this->addSql('UPDATE monster_weaknesses SET `condition` = _condition');
			$this->addSql('ALTER TABLE monster_weaknesses DROP _condition');
		}
	}
