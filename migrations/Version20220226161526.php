<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220226161526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE skipped_row DROP FOREIGN KEY FK_492C7384B6A263D9');
        $this->addSql('ALTER TABLE skipped_row ADD CONSTRAINT FK_492C7384B6A263D9 FOREIGN KEY (import_id) REFERENCES import (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rememberme_token CHANGE series series VARCHAR(88) NOT NULL COLLATE `utf8mb4_0900_ai_ci`, CHANGE value value VARCHAR(88) NOT NULL COLLATE `utf8mb4_0900_ai_ci`, CHANGE class class VARCHAR(100) NOT NULL COLLATE `utf8mb4_0900_ai_ci`, CHANGE username username VARCHAR(200) NOT NULL COLLATE `utf8mb4_0900_ai_ci`');
        $this->addSql('ALTER TABLE skipped_row DROP FOREIGN KEY FK_492C7384B6A263D9');
        $this->addSql('ALTER TABLE skipped_row ADD CONSTRAINT FK_492C7384B6A263D9 FOREIGN KEY (import_id) REFERENCES import (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
