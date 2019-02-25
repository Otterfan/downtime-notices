<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190225174237 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE application (id INT AUTO_INCREMENT NOT NULL, template_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, uptime_robot_code VARCHAR(255) DEFAULT NULL, automatic TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_A45BDDC15DA0FB8 (template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE priority (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, level SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, uid VARCHAR(180) NOT NULL, roles JSON NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649539B0606 (uid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_view (id INT AUTO_INCREMENT NOT NULL, notification_id INT NOT NULL, timestamp DATETIME NOT NULL, INDEX IDX_46C1E0B4EF1A9D84 (notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE template (id INT AUTO_INCREMENT NOT NULL, priority_id INT NOT NULL, type_id INT DEFAULT NULL, text LONGTEXT NOT NULL, INDEX IDX_97601F83497B19F9 (priority_id), INDEX IDX_97601F83C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, poster_id INT DEFAULT NULL, priority_id INT DEFAULT NULL, type_id INT DEFAULT NULL, application_id INT DEFAULT NULL, text LONGTEXT NOT NULL, start DATETIME NOT NULL, finish DATETIME DEFAULT NULL, INDEX IDX_BF5476CA5BB66C05 (poster_id), INDEX IDX_BF5476CA497B19F9 (priority_id), INDEX IDX_BF5476CAC54C8C93 (type_id), INDEX IDX_BF5476CA3E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC15DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id)');
        $this->addSql('ALTER TABLE notification_view ADD CONSTRAINT FK_46C1E0B4EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
        $this->addSql('ALTER TABLE template ADD CONSTRAINT FK_97601F83497B19F9 FOREIGN KEY (priority_id) REFERENCES priority (id)');
        $this->addSql('ALTER TABLE template ADD CONSTRAINT FK_97601F83C54C8C93 FOREIGN KEY (type_id) REFERENCES note_type (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA5BB66C05 FOREIGN KEY (poster_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA497B19F9 FOREIGN KEY (priority_id) REFERENCES priority (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAC54C8C93 FOREIGN KEY (type_id) REFERENCES note_type (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA3E030ACD FOREIGN KEY (application_id) REFERENCES application (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA3E030ACD');
        $this->addSql('ALTER TABLE template DROP FOREIGN KEY FK_97601F83497B19F9');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA497B19F9');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA5BB66C05');
        $this->addSql('ALTER TABLE template DROP FOREIGN KEY FK_97601F83C54C8C93');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAC54C8C93');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC15DA0FB8');
        $this->addSql('ALTER TABLE notification_view DROP FOREIGN KEY FK_46C1E0B4EF1A9D84');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE priority');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE note_type');
        $this->addSql('DROP TABLE notification_view');
        $this->addSql('DROP TABLE template');
        $this->addSql('DROP TABLE notification');
    }
}
