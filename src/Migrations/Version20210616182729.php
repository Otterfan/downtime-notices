<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210616182729 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE application CHANGE template_id template_id INT DEFAULT NULL, CHANGE uptime_robot_code uptime_robot_code VARCHAR(255) DEFAULT NULL, CHANGE on_status_page on_homepage TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE best_bet CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE best_bet_term CHANGE best_bet_id best_bet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification CHANGE poster_id poster_id INT DEFAULT NULL, CHANGE priority_id priority_id INT DEFAULT NULL, CHANGE type_id type_id INT DEFAULT NULL, CHANGE application_id application_id INT DEFAULT NULL, CHANGE finish finish DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE template CHANGE type_id type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE application CHANGE template_id template_id INT DEFAULT NULL, CHANGE uptime_robot_code uptime_robot_code VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE on_homepage on_status_page TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE best_bet CHANGE image image VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE best_bet_term CHANGE best_bet_id best_bet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification CHANGE poster_id poster_id INT DEFAULT NULL, CHANGE priority_id priority_id INT DEFAULT NULL, CHANGE type_id type_id INT DEFAULT NULL, CHANGE application_id application_id INT DEFAULT NULL, CHANGE finish finish DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE template CHANGE type_id type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
