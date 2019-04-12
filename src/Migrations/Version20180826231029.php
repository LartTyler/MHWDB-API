<?php declare(strict_types = 1);

	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180826231029 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE armor CHANGE armor_set_id armor_set_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL, CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL, CHANGE attributes attributes LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', CHANGE rarity rarity SMALLINT UNSIGNED NOT NULL');
			$this->addSql('DROP INDEX type_idx ON armor');
			$this->addSql('CREATE INDEX IDX_BF27FEFC8CDE5729 ON armor (type)');
			$this->addSql('ALTER TABLE armor_skill_ranks DROP FOREIGN KEY FK_101D79CC6CE3F9A6');
			$this->addSql('ALTER TABLE armor_skill_ranks DROP FOREIGN KEY FK_101D79CCF5AA3663');
			$this->addSql('ALTER TABLE armor_skill_ranks ADD CONSTRAINT FK_101D79CC6CE3F9A6 FOREIGN KEY (skill_rank_id) REFERENCES skill_ranks (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE armor_skill_ranks ADD CONSTRAINT FK_101D79CCF5AA3663 FOREIGN KEY (armor_id) REFERENCES armor (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE armor_slots DROP INDEX IDX_24FFC0B859E5119C, ADD UNIQUE INDEX UNIQ_24FFC0B859E5119C (slot_id)');
			$this->addSql('ALTER TABLE armor_slots DROP FOREIGN KEY FK_24FFC0B8F5AA3663');
			$this->addSql('ALTER TABLE armor_slots ADD CONSTRAINT FK_24FFC0B8F5AA3663 FOREIGN KEY (armor_id) REFERENCES armor (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE armor_assets CHANGE image_male_id image_male_id INT UNSIGNED DEFAULT NULL, CHANGE image_female_id image_female_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE armor_crafting_material_costs DROP FOREIGN KEY FK_8EE9EA58F5AA3663');
			$this->addSql('DROP INDEX IDX_8EE9EA58F5AA3663 ON armor_crafting_material_costs');
			$this->addSql('ALTER TABLE armor_crafting_material_costs DROP PRIMARY KEY');
			$this->addSql('ALTER TABLE armor_crafting_material_costs CHANGE armor_id armor_crafting_info_id INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE armor_crafting_material_costs ADD CONSTRAINT FK_8EE9EA587639D9A9 FOREIGN KEY (armor_crafting_info_id) REFERENCES armor_crafting_info (id) ON DELETE CASCADE');
			$this->addSql('CREATE INDEX IDX_8EE9EA587639D9A9 ON armor_crafting_material_costs (armor_crafting_info_id)');
			$this->addSql('ALTER TABLE armor_crafting_material_costs ADD PRIMARY KEY (armor_crafting_info_id, crafting_material_cost_id)');
			$this->addSql('ALTER TABLE armor_sets CHANGE bonus_id bonus_id INT UNSIGNED DEFAULT NULL, CHANGE rank rank VARCHAR(16) NOT NULL');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_FC4A63D15E237E06 ON armor_set_bonuses (name)');
			$this->addSql('ALTER TABLE assets CHANGE uri uri LONGTEXT NOT NULL');
			$this->addSql('DROP INDEX hash_idx ON assets');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_79D17D8E782B3D7070C66E9 ON assets (primary_hash, secondary_hash)');
			$this->addSql('ALTER TABLE charm_ranks CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL, CHANGE level level SMALLINT UNSIGNED NOT NULL');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_DF91C6555E237E06 ON charm_ranks (name)');
			$this->addSql('ALTER TABLE charm_ranks_skill_ranks DROP FOREIGN KEY FK_B86027C63BA5C9D1');
			$this->addSql('ALTER TABLE charm_ranks_skill_ranks DROP FOREIGN KEY FK_B86027C66CE3F9A6');
			$this->addSql('ALTER TABLE charm_ranks_skill_ranks ADD CONSTRAINT FK_B86027C63BA5C9D1 FOREIGN KEY (charm_rank_id) REFERENCES charm_ranks (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE charm_ranks_skill_ranks ADD CONSTRAINT FK_B86027C66CE3F9A6 FOREIGN KEY (skill_rank_id) REFERENCES skill_ranks (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs DROP FOREIGN KEY FK_8EEF42523BA5C9D1');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs DROP FOREIGN KEY FK_8EEF4252DD94392C');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs DROP PRIMARY KEY');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs CHANGE charm_rank_id charm_rank_crafting_info_id INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs ADD CONSTRAINT FK_8EEF4252357A2F3 FOREIGN KEY (charm_rank_crafting_info_id) REFERENCES charm_rank_crafting_info (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs ADD CONSTRAINT FK_8EEF4252DD94392C FOREIGN KEY (crafting_material_cost_id) REFERENCES crafting_material_costs (id) ON DELETE CASCADE');
			$this->addSql('CREATE INDEX IDX_8EEF4252357A2F3 ON charm_rank_crafting_info_crafting_material_costs (charm_rank_crafting_info_id)');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs ADD PRIMARY KEY (charm_rank_crafting_info_id, crafting_material_cost_id)');
			$this->addSql('ALTER TABLE decorations CHANGE skills_length skills_length INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE decorations_skill_ranks DROP FOREIGN KEY FK_5D2A04FA3446DFC4');
			$this->addSql('ALTER TABLE decorations_skill_ranks DROP FOREIGN KEY FK_5D2A04FA6CE3F9A6');
			$this->addSql('ALTER TABLE decorations_skill_ranks ADD CONSTRAINT FK_5D2A04FA3446DFC4 FOREIGN KEY (decoration_id) REFERENCES decorations (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE decorations_skill_ranks ADD CONSTRAINT FK_5D2A04FA6CE3F9A6 FOREIGN KEY (skill_rank_id) REFERENCES skill_ranks (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE items DROP sell_price, DROP buy_price');
			$this->addSql('ALTER TABLE motion_values CHANGE damage_type damage_type VARCHAR(32) DEFAULT NULL, CHANGE stun_potency stun_potency SMALLINT UNSIGNED DEFAULT NULL, CHANGE exhaust_potency exhaust_potency SMALLINT UNSIGNED DEFAULT NULL, CHANGE hits hits LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
			$this->addSql('DROP INDEX name_weapon_type_idx ON motion_values');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_672789F734C1BFD65E237E06 ON motion_values (weapon_type, name)');
			$this->addSql('DROP INDEX skill_level_idx ON skill_ranks');
			$this->addSql('ALTER TABLE skill_ranks CHANGE modifiers modifiers LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
			$this->addSql('ALTER TABLE weapons DROP FOREIGN KEY FK_520EBBE1537ED785');
			$this->addSql('DROP INDEX UNIQ_520EBBE1537ED785 ON weapons');
			$this->addSql('ALTER TABLE weapons DROP sharpness_id, CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL, CHANGE attributes attributes LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
			$this->addSql('DROP INDEX type_idx ON weapons');
			$this->addSql('CREATE INDEX IDX_520EBBE18CDE5729 ON weapons (type)');
			$this->addSql('ALTER TABLE weapon_slots DROP FOREIGN KEY FK_E5CEC5B559E5119C');
			$this->addSql('ALTER TABLE weapon_slots DROP FOREIGN KEY FK_E5CEC5B595B82273');
			$this->addSql('ALTER TABLE weapon_slots ADD CONSTRAINT FK_E5CEC5B559E5119C FOREIGN KEY (slot_id) REFERENCES slots (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE weapon_slots ADD CONSTRAINT FK_E5CEC5B595B82273 FOREIGN KEY (weapon_id) REFERENCES weapons (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE weapon_durability DROP FOREIGN KEY FK_F83322FA537ED785');
			$this->addSql('ALTER TABLE weapon_durability DROP FOREIGN KEY FK_F83322FA95B82273');
			$this->addSql('DROP INDEX IDX_F83322FA537ED785 ON weapon_durability');
			$this->addSql('ALTER TABLE weapon_durability DROP PRIMARY KEY');
			$this->addSql('ALTER TABLE weapon_durability CHANGE sharpness_id weapon_sharpness_id INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE weapon_durability ADD CONSTRAINT FK_F83322FA974DCA88 FOREIGN KEY (weapon_sharpness_id) REFERENCES weapon_sharpnesses (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE weapon_durability ADD CONSTRAINT FK_F83322FA95B82273 FOREIGN KEY (weapon_id) REFERENCES weapons (id) ON DELETE CASCADE');
			$this->addSql('CREATE INDEX IDX_F83322FA974DCA88 ON weapon_durability (weapon_sharpness_id)');
			$this->addSql('ALTER TABLE weapon_durability ADD PRIMARY KEY (weapon_id, weapon_sharpness_id)');
			$this->addSql('ALTER TABLE weapon_assets CHANGE icon_id icon_id INT UNSIGNED DEFAULT NULL, CHANGE image_id image_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapon_crafting_info CHANGE previous_id previous_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches DROP FOREIGN KEY FK_960A7F5CDCD6CC49');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches DROP FOREIGN KEY FK_960A7F5CD4C2CCD2');
			$this->addSql('DROP INDEX IDX_960A7F5CDCD6CC49 ON weapon_crafting_info_branches');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches DROP PRIMARY KEY');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches CHANGE branch_id weapon_id INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches ADD CONSTRAINT FK_960A7F5C95B82273 FOREIGN KEY (weapon_id) REFERENCES weapons (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches ADD CONSTRAINT FK_960A7F5CD4C2CCD2 FOREIGN KEY (weapon_crafting_info_id) REFERENCES weapon_crafting_info (id) ON DELETE CASCADE');
			$this->addSql('CREATE INDEX IDX_960A7F5C95B82273 ON weapon_crafting_info_branches (weapon_id)');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches ADD PRIMARY KEY (weapon_crafting_info_id, weapon_id)');
			$this->addSql('ALTER TABLE weapon_crafting_info_crafting_material_costs DROP FOREIGN KEY FK_DCB3F113D4C2CCD2');
			$this->addSql('ALTER TABLE weapon_crafting_info_crafting_material_costs DROP FOREIGN KEY FK_DCB3F113DD94392C');
			$this->addSql('ALTER TABLE weapon_crafting_info_crafting_material_costs ADD CONSTRAINT FK_DCB3F113D4C2CCD2 FOREIGN KEY (weapon_crafting_info_id) REFERENCES weapon_crafting_info (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE weapon_crafting_info_crafting_material_costs ADD CONSTRAINT FK_DCB3F113DD94392C FOREIGN KEY (crafting_material_cost_id) REFERENCES crafting_material_costs (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE weapon_crafting_info_upgrade_material_costs DROP FOREIGN KEY FK_B0AB8CFDD4C2CCD2');
			$this->addSql('ALTER TABLE weapon_crafting_info_upgrade_material_costs DROP FOREIGN KEY FK_B0AB8CFDDD94392C');
			$this->addSql('ALTER TABLE weapon_crafting_info_upgrade_material_costs ADD CONSTRAINT FK_B0AB8CFDD4C2CCD2 FOREIGN KEY (weapon_crafting_info_id) REFERENCES weapon_crafting_info (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE weapon_crafting_info_upgrade_material_costs ADD CONSTRAINT FK_B0AB8CFDDD94392C FOREIGN KEY (crafting_material_cost_id) REFERENCES crafting_material_costs (id) ON DELETE CASCADE');
			$this->addSql('DROP INDEX element_idx ON weapon_elements');
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf($this->connection->getDatabasePlatform()->getName() !==
				'mysql', 'Migration can only be executed safely on \'mysql\'.');

			$this->addSql('ALTER TABLE armor CHANGE armor_set_id armor_set_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL, CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL, CHANGE rarity rarity SMALLINT NOT NULL, CHANGE attributes attributes LONGTEXT NOT NULL COLLATE utf8mb4_bin');
			$this->addSql('DROP INDEX idx_bf27fefc8cde5729 ON armor');
			$this->addSql('CREATE INDEX type_idx ON armor (type)');
			$this->addSql('ALTER TABLE armor_assets CHANGE image_male_id image_male_id INT UNSIGNED DEFAULT NULL, CHANGE image_female_id image_female_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE armor_crafting_material_costs DROP FOREIGN KEY FK_8EE9EA587639D9A9');
			$this->addSql('DROP INDEX IDX_8EE9EA587639D9A9 ON armor_crafting_material_costs');
			$this->addSql('ALTER TABLE armor_crafting_material_costs DROP PRIMARY KEY');
			$this->addSql('ALTER TABLE armor_crafting_material_costs CHANGE armor_crafting_info_id armor_id INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE armor_crafting_material_costs ADD CONSTRAINT FK_8EE9EA58F5AA3663 FOREIGN KEY (armor_id) REFERENCES armor_crafting_info (id)');
			$this->addSql('CREATE INDEX IDX_8EE9EA58F5AA3663 ON armor_crafting_material_costs (armor_id)');
			$this->addSql('ALTER TABLE armor_crafting_material_costs ADD PRIMARY KEY (armor_id, crafting_material_cost_id)');
			$this->addSql('DROP INDEX UNIQ_FC4A63D15E237E06 ON armor_set_bonuses');
			$this->addSql('ALTER TABLE armor_sets CHANGE bonus_id bonus_id INT UNSIGNED DEFAULT NULL, CHANGE rank rank VARCHAR(32) NOT NULL COLLATE utf8_unicode_ci');
			$this->addSql('ALTER TABLE armor_skill_ranks DROP FOREIGN KEY FK_101D79CCF5AA3663');
			$this->addSql('ALTER TABLE armor_skill_ranks DROP FOREIGN KEY FK_101D79CC6CE3F9A6');
			$this->addSql('ALTER TABLE armor_skill_ranks ADD CONSTRAINT FK_101D79CCF5AA3663 FOREIGN KEY (armor_id) REFERENCES armor (id)');
			$this->addSql('ALTER TABLE armor_skill_ranks ADD CONSTRAINT FK_101D79CC6CE3F9A6 FOREIGN KEY (skill_rank_id) REFERENCES skill_ranks (id)');
			$this->addSql('ALTER TABLE armor_slots DROP INDEX UNIQ_24FFC0B859E5119C, ADD INDEX IDX_24FFC0B859E5119C (slot_id)');
			$this->addSql('ALTER TABLE armor_slots DROP FOREIGN KEY FK_24FFC0B8F5AA3663');
			$this->addSql('ALTER TABLE armor_slots ADD CONSTRAINT FK_24FFC0B8F5AA3663 FOREIGN KEY (armor_id) REFERENCES armor (id)');
			$this->addSql('ALTER TABLE assets CHANGE uri uri VARCHAR(254) NOT NULL COLLATE utf8_unicode_ci');
			$this->addSql('DROP INDEX uniq_79d17d8e782b3d7070c66e9 ON assets');
			$this->addSql('CREATE UNIQUE INDEX hash_idx ON assets (primary_hash, secondary_hash)');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs DROP FOREIGN KEY FK_8EEF4252357A2F3');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs DROP FOREIGN KEY FK_8EEF4252DD94392C');
			$this->addSql('DROP INDEX IDX_8EEF4252357A2F3 ON charm_rank_crafting_info_crafting_material_costs');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs DROP PRIMARY KEY');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs CHANGE charm_rank_crafting_info_id charm_rank_id INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs ADD CONSTRAINT FK_8EEF42523BA5C9D1 FOREIGN KEY (charm_rank_id) REFERENCES charm_rank_crafting_info (id)');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs ADD CONSTRAINT FK_8EEF4252DD94392C FOREIGN KEY (crafting_material_cost_id) REFERENCES crafting_material_costs (id)');
			$this->addSql('ALTER TABLE charm_rank_crafting_info_crafting_material_costs ADD PRIMARY KEY (charm_rank_id, crafting_material_cost_id)');
			$this->addSql('DROP INDEX UNIQ_DF91C6555E237E06 ON charm_ranks');
			$this->addSql('ALTER TABLE charm_ranks CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL, CHANGE level level SMALLINT NOT NULL');
			$this->addSql('ALTER TABLE charm_ranks_skill_ranks DROP FOREIGN KEY FK_B86027C63BA5C9D1');
			$this->addSql('ALTER TABLE charm_ranks_skill_ranks DROP FOREIGN KEY FK_B86027C66CE3F9A6');
			$this->addSql('ALTER TABLE charm_ranks_skill_ranks ADD CONSTRAINT FK_B86027C63BA5C9D1 FOREIGN KEY (charm_rank_id) REFERENCES charm_ranks (id)');
			$this->addSql('ALTER TABLE charm_ranks_skill_ranks ADD CONSTRAINT FK_B86027C66CE3F9A6 FOREIGN KEY (skill_rank_id) REFERENCES skill_ranks (id)');
			$this->addSql('ALTER TABLE decorations CHANGE skills_length skills_length INT UNSIGNED DEFAULT 0 NOT NULL');
			$this->addSql('ALTER TABLE decorations_skill_ranks DROP FOREIGN KEY FK_5D2A04FA3446DFC4');
			$this->addSql('ALTER TABLE decorations_skill_ranks DROP FOREIGN KEY FK_5D2A04FA6CE3F9A6');
			$this->addSql('ALTER TABLE decorations_skill_ranks ADD CONSTRAINT FK_5D2A04FA3446DFC4 FOREIGN KEY (decoration_id) REFERENCES decorations (id)');
			$this->addSql('ALTER TABLE decorations_skill_ranks ADD CONSTRAINT FK_5D2A04FA6CE3F9A6 FOREIGN KEY (skill_rank_id) REFERENCES skill_ranks (id)');
			$this->addSql('ALTER TABLE items ADD sell_price INT UNSIGNED NOT NULL, ADD buy_price INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE motion_values CHANGE damage_type damage_type VARCHAR(32) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE stun_potency stun_potency SMALLINT UNSIGNED DEFAULT NULL, CHANGE exhaust_potency exhaust_potency SMALLINT UNSIGNED DEFAULT NULL, CHANGE hits hits LONGTEXT NOT NULL COLLATE utf8mb4_bin');
			$this->addSql('DROP INDEX uniq_672789f734c1bfd65e237e06 ON motion_values');
			$this->addSql('CREATE UNIQUE INDEX name_weapon_type_idx ON motion_values (weapon_type, name)');
			$this->addSql('ALTER TABLE skill_ranks CHANGE modifiers modifiers LONGTEXT NOT NULL COLLATE utf8mb4_bin');
			$this->addSql('CREATE UNIQUE INDEX skill_level_idx ON skill_ranks (skill_id, level)');
			$this->addSql('ALTER TABLE weapon_assets CHANGE icon_id icon_id INT UNSIGNED DEFAULT NULL, CHANGE image_id image_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapon_crafting_info CHANGE previous_id previous_id INT UNSIGNED DEFAULT NULL');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches DROP FOREIGN KEY FK_960A7F5C95B82273');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches DROP FOREIGN KEY FK_960A7F5CD4C2CCD2');
			$this->addSql('DROP INDEX IDX_960A7F5C95B82273 ON weapon_crafting_info_branches');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches DROP PRIMARY KEY');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches CHANGE weapon_id branch_id INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches ADD CONSTRAINT FK_960A7F5CDCD6CC49 FOREIGN KEY (branch_id) REFERENCES weapons (id)');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches ADD CONSTRAINT FK_960A7F5CD4C2CCD2 FOREIGN KEY (weapon_crafting_info_id) REFERENCES weapon_crafting_info (id)');
			$this->addSql('CREATE INDEX IDX_960A7F5CDCD6CC49 ON weapon_crafting_info_branches (branch_id)');
			$this->addSql('ALTER TABLE weapon_crafting_info_branches ADD PRIMARY KEY (weapon_crafting_info_id, branch_id)');
			$this->addSql('ALTER TABLE weapon_crafting_info_crafting_material_costs DROP FOREIGN KEY FK_DCB3F113D4C2CCD2');
			$this->addSql('ALTER TABLE weapon_crafting_info_crafting_material_costs DROP FOREIGN KEY FK_DCB3F113DD94392C');
			$this->addSql('ALTER TABLE weapon_crafting_info_crafting_material_costs ADD CONSTRAINT FK_DCB3F113D4C2CCD2 FOREIGN KEY (weapon_crafting_info_id) REFERENCES weapon_crafting_info (id)');
			$this->addSql('ALTER TABLE weapon_crafting_info_crafting_material_costs ADD CONSTRAINT FK_DCB3F113DD94392C FOREIGN KEY (crafting_material_cost_id) REFERENCES crafting_material_costs (id)');
			$this->addSql('ALTER TABLE weapon_crafting_info_upgrade_material_costs DROP FOREIGN KEY FK_B0AB8CFDD4C2CCD2');
			$this->addSql('ALTER TABLE weapon_crafting_info_upgrade_material_costs DROP FOREIGN KEY FK_B0AB8CFDDD94392C');
			$this->addSql('ALTER TABLE weapon_crafting_info_upgrade_material_costs ADD CONSTRAINT FK_B0AB8CFDD4C2CCD2 FOREIGN KEY (weapon_crafting_info_id) REFERENCES weapon_crafting_info (id)');
			$this->addSql('ALTER TABLE weapon_crafting_info_upgrade_material_costs ADD CONSTRAINT FK_B0AB8CFDDD94392C FOREIGN KEY (crafting_material_cost_id) REFERENCES crafting_material_costs (id)');
			$this->addSql('ALTER TABLE weapon_durability DROP FOREIGN KEY FK_F83322FA974DCA88');
			$this->addSql('ALTER TABLE weapon_durability DROP FOREIGN KEY FK_F83322FA95B82273');
			$this->addSql('DROP INDEX IDX_F83322FA974DCA88 ON weapon_durability');
			$this->addSql('ALTER TABLE weapon_durability DROP PRIMARY KEY');
			$this->addSql('ALTER TABLE weapon_durability CHANGE weapon_sharpness_id sharpness_id INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE weapon_durability ADD CONSTRAINT FK_F83322FA537ED785 FOREIGN KEY (sharpness_id) REFERENCES weapon_sharpnesses (id)');
			$this->addSql('ALTER TABLE weapon_durability ADD CONSTRAINT FK_F83322FA95B82273 FOREIGN KEY (weapon_id) REFERENCES weapons (id)');
			$this->addSql('CREATE INDEX IDX_F83322FA537ED785 ON weapon_durability (sharpness_id)');
			$this->addSql('ALTER TABLE weapon_durability ADD PRIMARY KEY (weapon_id, sharpness_id)');
			$this->addSql('CREATE UNIQUE INDEX element_idx ON weapon_elements (weapon_id, type)');
			$this->addSql('ALTER TABLE weapon_slots DROP FOREIGN KEY FK_E5CEC5B595B82273');
			$this->addSql('ALTER TABLE weapon_slots DROP FOREIGN KEY FK_E5CEC5B559E5119C');
			$this->addSql('ALTER TABLE weapon_slots ADD CONSTRAINT FK_E5CEC5B595B82273 FOREIGN KEY (weapon_id) REFERENCES weapons (id)');
			$this->addSql('ALTER TABLE weapon_slots ADD CONSTRAINT FK_E5CEC5B559E5119C FOREIGN KEY (slot_id) REFERENCES slots (id)');
			$this->addSql('ALTER TABLE weapons ADD sharpness_id INT UNSIGNED NOT NULL, CHANGE crafting_id crafting_id INT UNSIGNED DEFAULT NULL, CHANGE assets_id assets_id INT UNSIGNED DEFAULT NULL, CHANGE attributes attributes LONGTEXT NOT NULL COLLATE utf8mb4_bin');
			$this->addSql('ALTER TABLE weapons ADD CONSTRAINT FK_520EBBE1537ED785 FOREIGN KEY (sharpness_id) REFERENCES weapon_sharpnesses (id)');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_520EBBE1537ED785 ON weapons (sharpness_id)');
			$this->addSql('DROP INDEX idx_520ebbe18cde5729 ON weapons');
			$this->addSql('CREATE INDEX type_idx ON weapons (type)');
		}
	}
