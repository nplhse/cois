<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210930142438 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE import ADD hospital_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE import ADD CONSTRAINT FK_9D4ECE1D63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('CREATE INDEX IDX_9D4ECE1D63DBB69 ON import (hospital_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE import DROP FOREIGN KEY FK_9D4ECE1D63DBB69');
        $this->addSql('DROP INDEX IDX_9D4ECE1D63DBB69 ON import');
        $this->addSql('ALTER TABLE import DROP hospital_id');
    }
}
