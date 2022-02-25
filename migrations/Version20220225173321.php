<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220225173321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE skipped_row (id INT AUTO_INCREMENT NOT NULL, import_id INT NOT NULL, errors JSON NOT NULL, data JSON NOT NULL, INDEX IDX_492C7384B6A263D9 (import_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE skipped_row ADD CONSTRAINT FK_492C7384B6A263D9 FOREIGN KEY (import_id) REFERENCES import (id)');
        $this->addSql('ALTER TABLE import ADD skipped_rows INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE skipped_row');
        $this->addSql('ALTER TABLE import DROP skipped_rows');
        $this->addSql('ALTER TABLE rememberme_token CHANGE series series VARCHAR(88) NOT NULL COLLATE `utf8mb4_0900_ai_ci`, CHANGE value value VARCHAR(88) NOT NULL COLLATE `utf8mb4_0900_ai_ci`, CHANGE class class VARCHAR(100) NOT NULL COLLATE `utf8mb4_0900_ai_ci`, CHANGE username username VARCHAR(200) NOT NULL COLLATE `utf8mb4_0900_ai_ci`');
    }
}
