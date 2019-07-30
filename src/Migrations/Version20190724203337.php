<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190724203337 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE best_bet (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT NOT NULL, title VARCHAR(255) NOT NULL, created DATE NOT NULL, updated DATE NOT NULL, link LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE best_bet_term (id INT AUTO_INCREMENT NOT NULL, best_bet_id INT DEFAULT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_15846E09EBD580 (best_bet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE best_bet_term ADD CONSTRAINT FK_15846E09EBD580 FOREIGN KEY (best_bet_id) REFERENCES best_bet (id)');
        $this->addSql('ALTER TABLE application CHANGE template_id template_id INT DEFAULT NULL, CHANGE uptime_robot_code uptime_robot_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE template CHANGE type_id type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE notification CHANGE poster_id poster_id INT DEFAULT NULL, CHANGE priority_id priority_id INT DEFAULT NULL, CHANGE type_id type_id INT DEFAULT NULL, CHANGE application_id application_id INT DEFAULT NULL, CHANGE finish finish DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE best_bet_term DROP FOREIGN KEY FK_15846E09EBD580');
        $this->addSql('DROP TABLE best_bet');
        $this->addSql('DROP TABLE best_bet_term');
        $this->addSql('ALTER TABLE application CHANGE template_id template_id INT DEFAULT NULL, CHANGE uptime_robot_code uptime_robot_code VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE notification CHANGE poster_id poster_id INT DEFAULT NULL, CHANGE priority_id priority_id INT DEFAULT NULL, CHANGE type_id type_id INT DEFAULT NULL, CHANGE application_id application_id INT DEFAULT NULL, CHANGE finish finish DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE template CHANGE type_id type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
