<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210309230200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE import ADD duration DOUBLE PRECISION DEFAULT NULL, ADD last_run DATETIME DEFAULT NULL, ADD times_run INT DEFAULT NULL, ADD item_count INT DEFAULT NULL, ADD last_error VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE import DROP duration, DROP last_run, DROP times_run, DROP item_count, DROP last_error');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
