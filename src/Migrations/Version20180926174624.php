<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20180926174624 extends AbstractMigration {
		/**
		 * @var array
		 */
		private $weaponSlotsForDown = [];

		/**
		 * @var array
		 */
		private $armorSlotsForDown = [];

		/**
		 * @param Schema $schema
		 *
		 * @return void
		 */
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$statement = $this->connection->createQueryBuilder()
				->from('armor_slots', '_as')
				->leftJoin('_as', 'slots', 's', '_as.slot_id = s.id')
				->select('_as.armor_id, s.rank')
				->execute();

			$armorSlots = [];

			while ($row = $statement->fetch(\PDO::FETCH_OBJ)) {
				if (!isset($armorSlots[$row->armor_id]))
					$armorSlots[$row->armor_id] = [];

				$armorSlots[$row->armor_id][] = (int)$row->rank;
			}

			$this->addSql('ALTER TABLE armor_slots DROP FOREIGN KEY FK_24FFC0B859E5119C');
			$this->addSql('ALTER TABLE armor_slots DROP FOREIGN KEY FK_24FFC0B8F5AA3663');
			$this->addSql('DROP INDEX UNIQ_24FFC0B859E5119C ON armor_slots');
			$this->addSql('ALTER TABLE armor_slots DROP PRIMARY KEY');
			$this->addSql('TRUNCATE armor_slots');
			$this->addSql('ALTER TABLE armor_slots ADD id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL, ADD rank INT UNSIGNED NOT NULL, DROP slot_id');
			$this->addSql('ALTER TABLE armor_slots ADD CONSTRAINT FK_24FFC0B8F5AA3663 FOREIGN KEY (armor_id) REFERENCES armor (id)');

			foreach ($armorSlots as $armorId => $slots) {
				foreach ($slots as $rank) {
					$this->addSql('INSERT INTO armor_slots (armor_id, rank) VALUES (?, ?)', [
						$armorId,
						$rank,
					], [
						\PDO::PARAM_INT,
						\PDO::PARAM_INT,
					]);
				}
			}

			unset($armorSlots);

			$statement = $this->connection->createQueryBuilder()
				->from('weapon_slots', 'ws')
				->leftJoin('ws', 'slots', 's', 'ws.slot_id = s.id')
				->select('ws.weapon_id, s.rank')
				->execute();

			$weaponSlots = [];

			while ($row = $statement->fetch(\PDO::FETCH_OBJ)) {
				if (!isset($weaponSlots[$row->weapon_id]))
					$weaponSlots[$row->weapon_id] = [];

				$weaponSlots[$row->weapon_id][] = (int)$row->rank;
			}

			$this->addSql('ALTER TABLE weapon_slots DROP FOREIGN KEY FK_E5CEC5B559E5119C');
			$this->addSql('ALTER TABLE weapon_slots DROP FOREIGN KEY FK_E5CEC5B595B82273');
			$this->addSql('DROP INDEX IDX_E5CEC5B559E5119C ON weapon_slots');
			$this->addSql('ALTER TABLE weapon_slots DROP PRIMARY KEY');
			$this->addSql('TRUNCATE weapon_slots');
			$this->addSql('ALTER TABLE weapon_slots ADD id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL, ADD rank INT UNSIGNED NOT NULL, DROP slot_id');
			$this->addSql('ALTER TABLE weapon_slots ADD CONSTRAINT FK_E5CEC5B595B82273 FOREIGN KEY (weapon_id) REFERENCES weapons (id)');

			foreach ($weaponSlots as $weaponId => $slots) {
				foreach ($slots as $rank) {
					$this->addSql('INSERT INTO weapon_slots (weapon_id, rank) VALUES (?, ?)', [
						$weaponId,
						$rank,
					], [
						\PDO::PARAM_INT,
						\PDO::PARAM_INT,
					]);
				}
			}

			$this->addSql('DROP TABLE slots');
		}

		/**
		 * @param Schema $schema
		 *
		 * @return void
		 */
		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->write(
				'Reading existing slot data into memory. This data will be migrated into the old slot format during ' .
				'postDown()'
			);

			$statement = $this->connection->createQueryBuilder()
				->from('armor_slots', 's')
				->select('s.armor_id, s.rank')
				->execute();

			while ($row = $statement->fetch(\PDO::FETCH_OBJ)) {
				if (!isset($this->armorSlotsForDown[$row->armor_id]))
					$this->armorSlotsForDown[$row->armor_id] = [];

				$this->armorSlotsForDown[$row->armor_id][] = $row->rank;
			}

			$statement = $this->connection->createQueryBuilder()
				->from('weapon_slots', 's')
				->select('s.weapon_id, s.rank')
				->execute();

			while ($row = $statement->fetch(\PDO::FETCH_OBJ)) {
				if (!isset($this->weaponSlotsForDown[$row->weapon_id]))
					$this->weaponSlotsForDown[$row->weapon_id] = [];

				$this->weaponSlotsForDown[$row->weapon_id][] = $row->rank;
			}

			$this->addSql('CREATE TABLE slots (id INT UNSIGNED AUTO_INCREMENT NOT NULL, rank SMALLINT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

			$this->addSql('TRUNCATE armor_slots');
			$this->addSql('ALTER TABLE armor_slots MODIFY id INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE armor_slots DROP FOREIGN KEY FK_24FFC0B8F5AA3663');
			$this->addSql('ALTER TABLE armor_slots DROP PRIMARY KEY');
			$this->addSql('ALTER TABLE armor_slots ADD slot_id INT UNSIGNED NOT NULL, DROP id, DROP rank');
			$this->addSql('ALTER TABLE armor_slots ADD CONSTRAINT FK_24FFC0B859E5119C FOREIGN KEY (slot_id) REFERENCES slots (id)');
			$this->addSql('ALTER TABLE armor_slots ADD CONSTRAINT FK_24FFC0B8F5AA3663 FOREIGN KEY (armor_id) REFERENCES armor (id) ON DELETE CASCADE');
			$this->addSql('CREATE UNIQUE INDEX UNIQ_24FFC0B859E5119C ON armor_slots (slot_id)');
			$this->addSql('ALTER TABLE armor_slots ADD PRIMARY KEY (armor_id, slot_id)');

			$this->addSql('TRUNCATE weapon_slots');
			$this->addSql('ALTER TABLE weapon_slots MODIFY id INT UNSIGNED NOT NULL');
			$this->addSql('ALTER TABLE weapon_slots DROP FOREIGN KEY FK_E5CEC5B595B82273');
			$this->addSql('ALTER TABLE weapon_slots DROP PRIMARY KEY');
			$this->addSql('ALTER TABLE weapon_slots ADD slot_id INT UNSIGNED NOT NULL, DROP id, DROP rank');
			$this->addSql('ALTER TABLE weapon_slots ADD CONSTRAINT FK_E5CEC5B559E5119C FOREIGN KEY (slot_id) REFERENCES slots (id) ON DELETE CASCADE');
			$this->addSql('ALTER TABLE weapon_slots ADD CONSTRAINT FK_E5CEC5B595B82273 FOREIGN KEY (weapon_id) REFERENCES weapons (id) ON DELETE CASCADE');
			$this->addSql('CREATE INDEX IDX_E5CEC5B559E5119C ON weapon_slots (slot_id)');
			$this->addSql('ALTER TABLE weapon_slots ADD PRIMARY KEY (weapon_id, slot_id)');
		}

		/**
		 * @param Schema $schema
		 *
		 * @return void
		 * @throws \Doctrine\DBAL\DBALException
		 */
		public function postDown(Schema $schema): void {
			parent::postDown($schema);

			$this->write('Inserting armor slot data preserved during down()...');

			foreach ($this->armorSlotsForDown as $armorId => $slots) {
				foreach ($slots as $rank) {
					$this->connection->insert('slots', [
						'rank' => $rank,
					], [
						'rank' => \PDO::PARAM_INT,
					]);

					$this->connection->insert('armor_slots', [
						'armor_id' => $armorId,
						'slot_id' => $this->connection->lastInsertId(),
					], [
						\PDO::PARAM_INT,
						\PDO::PARAM_INT,
					]);
				}
			}

			$this->write('Inserting weapon slot data preserved during down()...');

			foreach ($this->weaponSlotsForDown as $weaponId => $slots) {
				foreach ($slots as $rank) {
					$this->connection->insert('slots', [
						'rank' => $rank,
					], [
						\PDO::PARAM_INT,
					]);

					$this->connection->insert('weapon_slots', [
						'weapon_id' => $weaponId,
						'slot_id' => $this->connection->lastInsertId(),
					], [
						\PDO::PARAM_INT,
						\PDO::PARAM_INT,
					]);
				}
			}
		}
	}
