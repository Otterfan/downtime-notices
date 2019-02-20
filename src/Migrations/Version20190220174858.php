<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190220174858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'sqlite',
            'Migration can only be executed safely on \'sqlite\'.'
        );

        $this->addSql(
            'CREATE TABLE priority (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, level SMALLINT NOT NULL)'
        );
        $this->addSql(
            'CREATE TABLE note_type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)'
        );
        $this->addSql(
            'CREATE TABLE template (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, priority_id INTEGER NOT NULL, type_id INTEGER DEFAULT NULL, text CLOB NOT NULL)'
        );
        $this->addSql('CREATE INDEX IDX_97601F83497B19F9 ON template (priority_id)');
        $this->addSql('CREATE INDEX IDX_97601F83C54C8C93 ON template (type_id)');
        $this->addSql('DROP INDEX IDX_46C1E0B4EF1A9D84');
        $this->addSql(
            'CREATE TEMPORARY TABLE __temp__notification_view AS SELECT id, notification_id, timestamp FROM notification_view'
        );
        $this->addSql('DROP TABLE notification_view');
        $this->addSql(
            'CREATE TABLE notification_view (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, notification_id INTEGER NOT NULL, timestamp DATETIME NOT NULL, CONSTRAINT FK_46C1E0B4EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id) NOT DEFERRABLE INITIALLY IMMEDIATE)'
        );
        $this->addSql(
            'INSERT INTO notification_view (id, notification_id, timestamp) SELECT id, notification_id, timestamp FROM __temp__notification_view'
        );
        $this->addSql('DROP TABLE __temp__notification_view');
        $this->addSql('CREATE INDEX IDX_46C1E0B4EF1A9D84 ON notification_view (notification_id)');
        $this->addSql('DROP INDEX IDX_BF5476CA5BB66C05');
        $this->addSql(
            'CREATE TEMPORARY TABLE __temp__notification AS SELECT id, poster_id, text, start, finish FROM notification'
        );
        $this->addSql('DROP TABLE notification');
        $this->addSql(
            'CREATE TABLE notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, poster_id INTEGER DEFAULT NULL, priority_id INTEGER DEFAULT NULL, type_id INTEGER DEFAULT NULL, text CLOB NOT NULL COLLATE BINARY, start DATETIME NOT NULL, finish DATETIME DEFAULT NULL, CONSTRAINT FK_BF5476CA5BB66C05 FOREIGN KEY (poster_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BF5476CA497B19F9 FOREIGN KEY (priority_id) REFERENCES priority (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BF5476CAC54C8C93 FOREIGN KEY (type_id) REFERENCES note_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE)'
        );
        $this->addSql(
            'INSERT INTO notification (id, poster_id, text, start, finish) SELECT id, poster_id, text, start, finish FROM __temp__notification'
        );
        $this->addSql('DROP TABLE __temp__notification');
        $this->addSql('CREATE INDEX IDX_BF5476CA5BB66C05 ON notification (poster_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA497B19F9 ON notification (priority_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAC54C8C93 ON notification (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'sqlite',
            'Migration can only be executed safely on \'sqlite\'.'
        );

        $this->addSql('DROP TABLE priority');
        $this->addSql('DROP TABLE note_type');
        $this->addSql('DROP TABLE template');
        $this->addSql('DROP INDEX IDX_BF5476CA5BB66C05');
        $this->addSql('DROP INDEX IDX_BF5476CA497B19F9');
        $this->addSql('DROP INDEX IDX_BF5476CAC54C8C93');
        $this->addSql(
            'CREATE TEMPORARY TABLE __temp__notification AS SELECT id, poster_id, text, start, finish FROM notification'
        );
        $this->addSql('DROP TABLE notification');
        $this->addSql(
            'CREATE TABLE notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, poster_id INTEGER DEFAULT NULL, text CLOB NOT NULL, start DATETIME NOT NULL, finish DATETIME DEFAULT NULL)'
        );
        $this->addSql(
            'INSERT INTO notification (id, poster_id, text, start, finish) SELECT id, poster_id, text, start, finish FROM __temp__notification'
        );
        $this->addSql('DROP TABLE __temp__notification');
        $this->addSql('CREATE INDEX IDX_BF5476CA5BB66C05 ON notification (poster_id)');
        $this->addSql('DROP INDEX IDX_46C1E0B4EF1A9D84');
        $this->addSql(
            'CREATE TEMPORARY TABLE __temp__notification_view AS SELECT id, notification_id, timestamp FROM notification_view'
        );
        $this->addSql('DROP TABLE notification_view');
        $this->addSql(
            'CREATE TABLE notification_view (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, notification_id INTEGER NOT NULL, timestamp DATETIME NOT NULL)'
        );
        $this->addSql(
            'INSERT INTO notification_view (id, notification_id, timestamp) SELECT id, notification_id, timestamp FROM __temp__notification_view'
        );
        $this->addSql('DROP TABLE __temp__notification_view');
        $this->addSql('CREATE INDEX IDX_46C1E0B4EF1A9D84 ON notification_view (notification_id)');
    }

    public function postUp(Schema $schema): void
    {
        parent::postUp($schema); // TODO: Change the autogenerated stub

        $priority_sql = <<<SQL
INSERT into priority (name, level)
VALUES ('low',1),('medium',2),('high',3);
SQL;
        $this->connection->executeQuery($priority_sql);

        $type_sql = <<<SQL
INSERT into note_type (name)
VALUES ('warning'),('closing'),('system down');
SQL;
        $this->connection->executeQuery($type_sql);
    }
}
