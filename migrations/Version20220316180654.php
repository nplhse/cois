<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220316180654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE allocation CHANGE creation_date creation_date VARCHAR(10) NOT NULL, CHANGE creation_time creation_time VARCHAR(5) NOT NULL, CHANGE arrival_date arrival_date VARCHAR(10) NOT NULL, CHANGE arrival_time arrival_time VARCHAR(5) NOT NULL, CHANGE mode_of_transport mode_of_transport VARCHAR(10) DEFAULT NULL, CHANGE indication indication VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE skipped_row CHANGE data data JSON NOT NULL');
        $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
        $this->addSql('ALTER TABLE messenger_messages CHANGE queue_name queue_name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE allocation CHANGE creation_date creation_date VARCHAR(50) NOT NULL, CHANGE creation_time creation_time VARCHAR(50) NOT NULL, CHANGE arrival_date arrival_date VARCHAR(50) NOT NULL, CHANGE arrival_time arrival_time VARCHAR(50) NOT NULL, CHANGE mode_of_transport mode_of_transport VARCHAR(10) NOT NULL, CHANGE indication indication VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE queue_name queue_name VARCHAR(190) NOT NULL');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('ALTER TABLE skipped_row CHANGE data data LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
