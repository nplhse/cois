<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220211170455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE import ADD type VARCHAR(255) NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD file_path VARCHAR(255) NOT NULL, ADD file_mime_type VARCHAR(255) NOT NULL, ADD file_extension VARCHAR(255) NOT NULL, ADD file_size INT NOT NULL, ADD row_count INT NOT NULL, ADD run_count INT NOT NULL, ADD runtime INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE import DROP type, DROP updated_at, DROP file_path, DROP file_mime_type, DROP file_extension, DROP file_size, DROP row_count, DROP run_count, DROP runtime');
    }

    public function isTransactional(): bool
    {
        return true;
    }
}
