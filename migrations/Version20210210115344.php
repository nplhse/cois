<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210210115344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE allocation (id INT AUTO_INCREMENT NOT NULL, hospital_id INT NOT NULL, dispatch_area VARCHAR(255) NOT NULL, supply_area VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, creation_date VARCHAR(255) NOT NULL, creation_time VARCHAR(255) NOT NULL, creation_day INT NOT NULL, creation_weekday VARCHAR(255) NOT NULL, creation_month INT NOT NULL, creation_year INT NOT NULL, creation_hour INT NOT NULL, creation_minute INT NOT NULL, arrival_at DATETIME NOT NULL, arrival_date VARCHAR(255) NOT NULL, arrival_time VARCHAR(255) NOT NULL, arrival_day INT NOT NULL, arrival_weekday VARCHAR(255) NOT NULL, arrival_month INT NOT NULL, arrival_year INT NOT NULL, arrival_hour INT NOT NULL, arrival_minute INT NOT NULL, requires_resus TINYINT(1) NOT NULL, requires_cathlab TINYINT(1) NOT NULL, occasion VARCHAR(255) DEFAULT NULL, gender VARCHAR(1) NOT NULL, age INT NOT NULL, is_cpr TINYINT(1) NOT NULL, is_ventilated TINYINT(1) NOT NULL, is_shock TINYINT(1) NOT NULL, is_infectious VARCHAR(255) NOT NULL, is_pregnant TINYINT(1) NOT NULL, is_with_physician TINYINT(1) NOT NULL, assignment VARCHAR(255) DEFAULT NULL, mode_of_transport VARCHAR(255) NOT NULL, comment VARCHAR(255) DEFAULT NULL, speciality VARCHAR(255) NOT NULL, speciality_detail VARCHAR(255) NOT NULL, handover_point VARCHAR(255) NOT NULL, speciality_was_closed TINYINT(1) NOT NULL, pzc INT NOT NULL, pzctext VARCHAR(255) NOT NULL, secondary_pzc INT DEFAULT NULL, secondary_pzctext VARCHAR(255) NOT NULL, INDEX IDX_5C44232A63DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE allocation ADD CONSTRAINT FK_5C44232A63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE allocation');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
