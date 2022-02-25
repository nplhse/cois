<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220225144938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE import DROP size, DROP path, DROP file, DROP extension, DROP mime_type, DROP is_fixture, DROP caption, DROP contents, DROP duration, DROP last_run, DROP times_run, DROP item_count');
        $this->addSql('ALTER TABLE user DROP is_credentials_expired, DROP allows_email, DROP allows_email_reminder, DROP toggle_alloc_sidebar');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE import ADD size INT NOT NULL, ADD path VARCHAR(255) NOT NULL, ADD file VARCHAR(255) DEFAULT NULL, ADD extension VARCHAR(255) NOT NULL, ADD mime_type VARCHAR(255) NOT NULL, ADD is_fixture TINYINT(1) NOT NULL, ADD caption VARCHAR(255) NOT NULL, ADD contents VARCHAR(255) NOT NULL, ADD duration DOUBLE PRECISION DEFAULT NULL, ADD last_run DATETIME DEFAULT NULL, ADD times_run INT DEFAULT NULL, ADD item_count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rememberme_token CHANGE series series VARCHAR(88) NOT NULL COLLATE `utf8mb4_0900_ai_ci`, CHANGE value value VARCHAR(88) NOT NULL COLLATE `utf8mb4_0900_ai_ci`, CHANGE class class VARCHAR(100) NOT NULL COLLATE `utf8mb4_0900_ai_ci`, CHANGE username username VARCHAR(200) NOT NULL COLLATE `utf8mb4_0900_ai_ci`');
        $this->addSql('ALTER TABLE user ADD is_credentials_expired TINYINT(1) NOT NULL, ADD allows_email TINYINT(1) DEFAULT NULL, ADD allows_email_reminder TINYINT(1) DEFAULT NULL, ADD toggle_alloc_sidebar TINYINT(1) DEFAULT NULL');
    }
}
