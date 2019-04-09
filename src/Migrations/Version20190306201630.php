<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190306201630 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE application ADD on_status_page TINYINT(1) NOT NULL, ADD public_description LONGTEXT NOT NULL, ADD url LONGTEXT DEFAULT NULL, ADD public_name VARCHAR(255) NOT NULL, CHANGE template_id template_id INT DEFAULT NULL, CHANGE uptime_robot_code uptime_robot_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE template CHANGE type_id type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification CHANGE poster_id poster_id INT DEFAULT NULL, CHANGE priority_id priority_id INT DEFAULT NULL, CHANGE type_id type_id INT DEFAULT NULL, CHANGE application_id application_id INT DEFAULT NULL, CHANGE finish finish DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE application DROP on_status_page, DROP public_description, DROP url, DROP public_name, CHANGE template_id template_id INT DEFAULT NULL, CHANGE uptime_robot_code uptime_robot_code VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE notification CHANGE poster_id poster_id INT DEFAULT NULL, CHANGE priority_id priority_id INT DEFAULT NULL, CHANGE type_id type_id INT DEFAULT NULL, CHANGE application_id application_id INT DEFAULT NULL, CHANGE finish finish DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE template CHANGE type_id type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
