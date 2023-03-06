<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230306104630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX speciality_idx ON allocation (speciality)');
        $this->addSql('CREATE INDEX speciality_detail_idx ON allocation (speciality_detail)');
        $this->addSql('CREATE INDEX indication_idx ON allocation (indication)');
        $this->addSql('CREATE INDEX secondary_indication_idx ON allocation (secondary_indication)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX speciality_idx ON allocation');
        $this->addSql('DROP INDEX speciality_detail_idx ON allocation');
        $this->addSql('DROP INDEX indication_idx ON allocation');
        $this->addSql('DROP INDEX secondary_indication_idx ON allocation');
    }
}
