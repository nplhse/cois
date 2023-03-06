<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230306103647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX occasion_idx ON allocation (occasion)');
        $this->addSql('CREATE INDEX assignment_idx ON allocation (assignment)');
        $this->addSql('CREATE INDEX is_infectious_idx ON allocation (is_infectious)');
        $this->addSql('CREATE INDEX secondary_deployment_idx ON allocation (secondary_deployment)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX occasion_idx ON allocation');
        $this->addSql('DROP INDEX assignment_idx ON allocation');
        $this->addSql('DROP INDEX is_infectious_idx ON allocation');
        $this->addSql('DROP INDEX secondary_deployment_idx ON allocation');
    }
}
