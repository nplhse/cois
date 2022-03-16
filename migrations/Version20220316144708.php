<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220316144708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE allocation CHANGE creation_date creation_date VARCHAR(10) NOT NULL, CHANGE creation_time creation_time VARCHAR(5) NOT NULL, CHANGE arrival_date arrival_date VARCHAR(10) NOT NULL, CHANGE arrival_time arrival_time VARCHAR(5) NOT NULL, CHANGE assignment assignment VARCHAR(50) NOT NULL, CHANGE indication indication VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE allocation CHANGE creation_date creation_date VARCHAR(50) NOT NULL, CHANGE creation_time creation_time VARCHAR(50) NOT NULL, CHANGE arrival_date arrival_date VARCHAR(50) NOT NULL, CHANGE arrival_time arrival_time VARCHAR(50) NOT NULL, CHANGE assignment assignment VARCHAR(50) DEFAULT NULL, CHANGE indication indication VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE rememberme_token CHANGE series series VARCHAR(88) NOT NULL COLLATE `utf8mb4_0900_ai_ci`, CHANGE value value VARCHAR(88) NOT NULL COLLATE `utf8mb4_0900_ai_ci`, CHANGE class class VARCHAR(100) NOT NULL COLLATE `utf8mb4_0900_ai_ci`, CHANGE username username VARCHAR(200) NOT NULL COLLATE `utf8mb4_0900_ai_ci`');
    }
}
