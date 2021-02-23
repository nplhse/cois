<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210223211527 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE allocation ADD import_id INT DEFAULT NULL, ADD work_accident TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE allocation ADD CONSTRAINT FK_5C44232AB6A263D9 FOREIGN KEY (import_id) REFERENCES import (id)');
        $this->addSql('CREATE INDEX IDX_5C44232AB6A263D9 ON allocation (import_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE allocation DROP FOREIGN KEY FK_5C44232AB6A263D9');
        $this->addSql('DROP INDEX IDX_5C44232AB6A263D9 ON allocation');
        $this->addSql('ALTER TABLE allocation DROP import_id, DROP work_accident');
    }
}
