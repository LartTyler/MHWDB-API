<?php declare(strict_types = 1);
	namespace DoctrineMigrations;

	use Doctrine\DBAL\Schema\Schema;
	use Doctrine\Migrations\AbstractMigration;

	/**
	 * Auto-generated Migration: Please modify to your needs!
	 */
	final class Version20190402180838 extends AbstractMigration {
		public function up(Schema $schema): void {
			// this up() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('ALTER TABLE users ADD created_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD disabled TINYINT(1) NOT NULL, CHANGE password password VARCHAR(64) DEFAULT NULL');

			$this->addSql(
				'UPDATE users SET created_date = ? WHERE created_date IS NULL',
				[
					date('Y-m-d G:i:s'),
				],
				[
					\PDO::PARAM_STR,
				]
			);
		}

		public function down(Schema $schema): void {
			// this down() migration is auto-generated, please modify it to your needs
			$this->abortIf(
				$this->connection->getDatabasePlatform()->getName() !== 'mysql',
				'Migration can only be executed safely on \'mysql\'.'
			);

			$this->addSql('ALTER TABLE users DROP created_date, DROP disabled, CHANGE password password VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci');
		}
	}
