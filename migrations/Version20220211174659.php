<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220211174659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE allocation ADD state_id INT DEFAULT NULL, ADD dispatch_area_id INT DEFAULT NULL, ADD supply_area_id INT DEFAULT NULL, ADD urgency INT NOT NULL, ADD indication VARCHAR(50) DEFAULT NULL, ADD indication_code INT NOT NULL, ADD secondary_indication VARCHAR(50) DEFAULT NULL, ADD secondary_indication_code INT NOT NULL, DROP dispatch_area, DROP supply_area, CHANGE creation_date creation_date VARCHAR(50) NOT NULL, CHANGE creation_time creation_time VARCHAR(50) NOT NULL, CHANGE creation_weekday creation_weekday VARCHAR(10) NOT NULL, CHANGE arrival_date arrival_date VARCHAR(50) NOT NULL, CHANGE arrival_time arrival_time VARCHAR(50) NOT NULL, CHANGE arrival_weekday arrival_weekday VARCHAR(10) NOT NULL, CHANGE occasion occasion VARCHAR(50) DEFAULT NULL, CHANGE is_infectious is_infectious VARCHAR(50) NOT NULL, CHANGE assignment assignment VARCHAR(50) DEFAULT NULL, CHANGE mode_of_transport mode_of_transport VARCHAR(10) NOT NULL, CHANGE speciality speciality VARCHAR(50) NOT NULL, CHANGE speciality_detail speciality_detail VARCHAR(50) NOT NULL, CHANGE handover_point handover_point VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE allocation ADD CONSTRAINT FK_5C44232A5D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE allocation ADD CONSTRAINT FK_5C44232A957FD192 FOREIGN KEY (dispatch_area_id) REFERENCES dispatch_area (id)');
        $this->addSql('ALTER TABLE allocation ADD CONSTRAINT FK_5C44232A1B81C31 FOREIGN KEY (supply_area_id) REFERENCES supply_area (id)');
        $this->addSql('CREATE INDEX IDX_5C44232A5D83CC1 ON allocation (state_id)');
        $this->addSql('CREATE INDEX IDX_5C44232A957FD192 ON allocation (dispatch_area_id)');
        $this->addSql('CREATE INDEX IDX_5C44232A1B81C31 ON allocation (supply_area_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE allocation DROP FOREIGN KEY FK_5C44232A5D83CC1');
        $this->addSql('ALTER TABLE allocation DROP FOREIGN KEY FK_5C44232A957FD192');
        $this->addSql('ALTER TABLE allocation DROP FOREIGN KEY FK_5C44232A1B81C31');
        $this->addSql('DROP INDEX IDX_5C44232A5D83CC1 ON allocation');
        $this->addSql('DROP INDEX IDX_5C44232A957FD192 ON allocation');
        $this->addSql('DROP INDEX IDX_5C44232A1B81C31 ON allocation');
        $this->addSql('ALTER TABLE allocation ADD dispatch_area VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD supply_area VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP state_id, DROP dispatch_area_id, DROP supply_area_id, DROP urgency, DROP indication, DROP indication_code, DROP secondary_indication, DROP secondary_indication_code, CHANGE creation_date creation_date VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE creation_time creation_time VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE creation_weekday creation_weekday VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE arrival_date arrival_date VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE arrival_time arrival_time VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE arrival_weekday arrival_weekday VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE occasion occasion VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE assignment assignment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE is_infectious is_infectious VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE mode_of_transport mode_of_transport VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE speciality speciality VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE speciality_detail speciality_detail VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE handover_point handover_point VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }

    public function isTransactional(): bool
    {
        return true;
    }
}
