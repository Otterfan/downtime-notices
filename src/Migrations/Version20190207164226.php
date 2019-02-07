<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190207164226 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, uid, last_name, first_name, email FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, uid VARCHAR(25) NOT NULL COLLATE BINARY, last_name VARCHAR(255) NOT NULL COLLATE BINARY, first_name VARCHAR(255) NOT NULL COLLATE BINARY, email VARCHAR(255) NOT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO user (id, uid, last_name, first_name, email) SELECT id, uid, last_name, first_name, email FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649C808BA5A ON user (last_name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
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

        $this->addSql('DROP INDEX IDX_BF5476CA5BB66C05');
        $this->addSql('CREATE TEMPORARY TABLE __temp__notification AS SELECT id, poster_id, text, start, finish FROM notification');
        $this->addSql('DROP TABLE notification');
        $this->addSql('CREATE TABLE notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, poster_id INTEGER DEFAULT NULL, text CLOB NOT NULL, start DATETIME NOT NULL, finish DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO notification (id, poster_id, text, start, finish) SELECT id, poster_id, text, start, finish FROM __temp__notification');
        $this->addSql('DROP TABLE __temp__notification');
        $this->addSql('CREATE INDEX IDX_BF5476CA5BB66C05 ON notification (poster_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649C808BA5A');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, uid, last_name, first_name, email FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, uid VARCHAR(25) NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO user (id, uid, last_name, first_name, email) SELECT id, uid, last_name, first_name, email FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
    }
}
