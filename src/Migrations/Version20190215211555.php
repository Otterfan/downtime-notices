<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190215211555 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE notification_view (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, notification_id INTEGER NOT NULL, timestamp DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_46C1E0B4EF1A9D84 ON notification_view (notification_id)');
        $this->addSql('DROP INDEX IDX_BF5476CA5BB66C05');
        $this->addSql('CREATE TEMPORARY TABLE __temp__notification AS SELECT id, poster_id, text, start, finish FROM notification');
        $this->addSql('DROP TABLE notification');
        $this->addSql('CREATE TABLE notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, poster_id INTEGER DEFAULT NULL, text CLOB NOT NULL COLLATE BINARY, start DATETIME NOT NULL, finish DATETIME DEFAULT NULL, CONSTRAINT FK_BF5476CA5BB66C05 FOREIGN KEY (poster_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO notification (id, poster_id, text, start, finish) SELECT id, poster_id, text, start, finish FROM __temp__notification');
        $this->addSql('DROP TABLE __temp__notification');
        $this->addSql('CREATE INDEX IDX_BF5476CA5BB66C05 ON notification (poster_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE notification_view');
        $this->addSql('DROP INDEX IDX_BF5476CA5BB66C05');
        $this->addSql('CREATE TEMPORARY TABLE __temp__notification AS SELECT id, poster_id, text, start, finish FROM notification');
        $this->addSql('DROP TABLE notification');
        $this->addSql('CREATE TABLE notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, poster_id INTEGER DEFAULT NULL, text CLOB NOT NULL, start DATETIME NOT NULL, finish DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO notification (id, poster_id, text, start, finish) SELECT id, poster_id, text, start, finish FROM __temp__notification');
        $this->addSql('DROP TABLE __temp__notification');
        $this->addSql('CREATE INDEX IDX_BF5476CA5BB66C05 ON notification (poster_id)');
    }
}
