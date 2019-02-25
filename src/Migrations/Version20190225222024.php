<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190225222024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'ALTER TABLE application CHANGE template_id template_id INT DEFAULT NULL, CHANGE uptime_robot_code uptime_robot_code VARCHAR(255) DEFAULT NULL'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62A6DC275E237E06 ON priority (name)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2CA446715E237E06 ON note_type (name)');
        $this->addSql('ALTER TABLE template CHANGE type_id type_id INT DEFAULT NULL');
        $this->addSql(
            'ALTER TABLE notification CHANGE poster_id poster_id INT DEFAULT NULL, CHANGE priority_id priority_id INT DEFAULT NULL, CHANGE type_id type_id INT DEFAULT NULL, CHANGE application_id application_id INT DEFAULT NULL, CHANGE finish finish DATETIME DEFAULT NULL'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'ALTER TABLE application CHANGE template_id template_id INT DEFAULT NULL, CHANGE uptime_robot_code uptime_robot_code VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci'
        );
        $this->addSql('DROP INDEX UNIQ_2CA446715E237E06 ON note_type');
        $this->addSql(
            'ALTER TABLE notification CHANGE poster_id poster_id INT DEFAULT NULL, CHANGE priority_id priority_id INT DEFAULT NULL, CHANGE type_id type_id INT DEFAULT NULL, CHANGE application_id application_id INT DEFAULT NULL, CHANGE finish finish DATETIME DEFAULT \'NULL\''
        );
        $this->addSql('DROP INDEX UNIQ_62A6DC275E237E06 ON priority');
        $this->addSql('ALTER TABLE template CHANGE type_id type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }

    public function postUp(Schema $schema): void
    {
        $this->connection->executeQuery("INSERT INTO priority (name, level) VALUES ('high',1), ('medium',2), ('low',3)");
        $this->connection->executeQuery("INSERT INTO note_type (name) VALUES ('system down'), ('closure'), ('other')");

        $this->addFirstUser();
    }

    private function addFirstUser(): void
    {
        $first_user = [
            'uid'   => getenv('FIRST_USER_UID'),
            'lname' => getenv('FIRST_USER_LNAME'),
            'fname' => getenv('FIRST_USER_FNAME'),
            'email' => getenv('FIRST_USER_EMAIL')
        ];

        $add_user_sql = <<<SQL
INSERT INTO notices.user (uid, roles, last_name, first_name, email)
VALUES ('{$first_user['uid']}', '[]', '{$first_user['lname']}', '{$first_user['fname']}', '{$first_user['email']}');
SQL;

        echo "$add_user_sql\n";

        $this->connection->executeQuery($add_user_sql);
    }
}


