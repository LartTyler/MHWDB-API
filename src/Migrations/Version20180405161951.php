<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180405161951 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE armor_assets (id INT UNSIGNED AUTO_INCREMENT NOT NULL, image_male_id INT UNSIGNED NOT NULL, image_female_id INT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_3A541F11E88FF827 (image_male_id), UNIQUE INDEX UNIQ_3A541F11690AE98E (image_female_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE assets (id INT UNSIGNED AUTO_INCREMENT NOT NULL, uri VARCHAR(254) NOT NULL, primary_hash VARCHAR(128) NOT NULL, secondary_hash VARCHAR(128) NOT NULL, UNIQUE INDEX hash_idx (primary_hash, secondary_hash), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE weapon_assets (id INT UNSIGNED AUTO_INCREMENT NOT NULL, icon_id INT UNSIGNED NOT NULL, image_id INT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_442452A954B9D732 (icon_id), UNIQUE INDEX UNIQ_442452A93DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE armor_assets ADD CONSTRAINT FK_3A541F11E88FF827 FOREIGN KEY (image_male_id) REFERENCES assets (id)');
        $this->addSql('ALTER TABLE armor_assets ADD CONSTRAINT FK_3A541F11690AE98E FOREIGN KEY (image_female_id) REFERENCES assets (id)');
        $this->addSql('ALTER TABLE weapon_assets ADD CONSTRAINT FK_442452A954B9D732 FOREIGN KEY (icon_id) REFERENCES assets (id)');
        $this->addSql('ALTER TABLE weapon_assets ADD CONSTRAINT FK_442452A93DA5256D FOREIGN KEY (image_id) REFERENCES assets (id)');
        $this->addSql('ALTER TABLE armor ADD assets_id INT UNSIGNED DEFAULT NULL, CHANGE armor_set_id armor_set_id INT UNSIGNED DEFAULT NULL, CHANGE attributes attributes JSON NOT NULL');
        $this->addSql('ALTER TABLE armor ADD CONSTRAINT FK_BF27FEFCE6AF163A FOREIGN KEY (assets_id) REFERENCES armor_assets (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BF27FEFCE6AF163A ON armor (assets_id)');
        $this->addSql('ALTER TABLE skills CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE skill_ranks CHANGE modifiers modifiers JSON NOT NULL');
        $this->addSql('ALTER TABLE weapons ADD assets_id INT UNSIGNED DEFAULT NULL, CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL, CHANGE attributes attributes JSON NOT NULL');
        $this->addSql('ALTER TABLE weapons ADD CONSTRAINT FK_520EBBE1E6AF163A FOREIGN KEY (assets_id) REFERENCES weapon_assets (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_520EBBE1E6AF163A ON weapons (assets_id)');
        $this->addSql('ALTER TABLE weapon_crafting_info CHANGE previous_id previous_id INT UNSIGNED DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE armor DROP FOREIGN KEY FK_BF27FEFCE6AF163A');
        $this->addSql('ALTER TABLE armor_assets DROP FOREIGN KEY FK_3A541F11E88FF827');
        $this->addSql('ALTER TABLE armor_assets DROP FOREIGN KEY FK_3A541F11690AE98E');
        $this->addSql('ALTER TABLE weapon_assets DROP FOREIGN KEY FK_442452A954B9D732');
        $this->addSql('ALTER TABLE weapon_assets DROP FOREIGN KEY FK_442452A93DA5256D');
        $this->addSql('ALTER TABLE weapons DROP FOREIGN KEY FK_520EBBE1E6AF163A');
        $this->addSql('DROP TABLE armor_assets');
        $this->addSql('DROP TABLE assets');
        $this->addSql('DROP TABLE weapon_assets');
        $this->addSql('DROP INDEX UNIQ_BF27FEFCE6AF163A ON armor');
        $this->addSql('ALTER TABLE armor DROP assets_id, CHANGE armor_set_id armor_set_id INT UNSIGNED DEFAULT NULL, CHANGE attributes attributes LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE skill_ranks CHANGE modifiers modifiers LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE skills CHANGE description description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE weapon_crafting_info CHANGE previous_id previous_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_520EBBE1E6AF163A ON weapons');
        $this->addSql('ALTER TABLE weapons DROP assets_id, CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL, CHANGE attributes attributes LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
