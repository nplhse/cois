<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220206182738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospital DROP INDEX UNIQ_4282C85B7E3C61F9, ADD INDEX IDX_4282C85B7E3C61F9 (owner_id)');
        $this->addSql('ALTER TABLE hospital ADD state_id INT DEFAULT NULL, ADD dispatch_area_id INT DEFAULT NULL, ADD supply_area_id INT DEFAULT NULL, DROP supply_area, DROP dispatch_area, CHANGE address address LONGTEXT NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE beds beds INT NOT NULL');
        $this->addSql('ALTER TABLE hospital ADD CONSTRAINT FK_4282C85B5D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE hospital ADD CONSTRAINT FK_4282C85B957FD192 FOREIGN KEY (dispatch_area_id) REFERENCES dispatch_area (id)');
        $this->addSql('ALTER TABLE hospital ADD CONSTRAINT FK_4282C85B1B81C31 FOREIGN KEY (supply_area_id) REFERENCES supply_area (id)');
        $this->addSql('CREATE INDEX IDX_4282C85B5D83CC1 ON hospital (state_id)');
        $this->addSql('CREATE INDEX IDX_4282C85B957FD192 ON hospital (dispatch_area_id)');
        $this->addSql('CREATE INDEX IDX_4282C85B1B81C31 ON hospital (supply_area_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospital DROP INDEX IDX_4282C85B7E3C61F9, ADD UNIQUE INDEX UNIQ_4282C85B7E3C61F9 (owner_id)');
        $this->addSql('ALTER TABLE hospital DROP FOREIGN KEY FK_4282C85B5D83CC1');
        $this->addSql('ALTER TABLE hospital DROP FOREIGN KEY FK_4282C85B957FD192');
        $this->addSql('ALTER TABLE hospital DROP FOREIGN KEY FK_4282C85B1B81C31');
        $this->addSql('DROP INDEX IDX_4282C85B5D83CC1 ON hospital');
        $this->addSql('DROP INDEX IDX_4282C85B957FD192 ON hospital');
        $this->addSql('DROP INDEX IDX_4282C85B1B81C31 ON hospital');
        $this->addSql('ALTER TABLE hospital ADD supply_area VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD dispatch_area VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP state_id, DROP dispatch_area_id, DROP supply_area_id, CHANGE address address LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE beds beds INT DEFAULT NULL');
    }

    public function isTransactional(): bool
    {
        return true;
    }
}
