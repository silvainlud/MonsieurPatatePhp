<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211223200824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE planning_log (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', action_type VARCHAR(10) NOT NULL, planning_uuid VARCHAR(64) NOT NULL, title_previous VARCHAR(255) NOT NULL, description_previous LONGTEXT NOT NULL, date_start_previous DATETIME NOT NULL, date_end_previous DATETIME NOT NULL, teacher_previous VARCHAR(255) NOT NULL, location_previous VARCHAR(255) NOT NULL, title_next VARCHAR(255) NOT NULL, description_next LONGTEXT NOT NULL, date_start_next DATETIME NOT NULL, date_end_next DATETIME NOT NULL, teacher_next VARCHAR(255) NOT NULL, location_next VARCHAR(255) NOT NULL, date_create DATETIME NOT NULL, is_discord_send TINYINT(1) NOT NULL, updated_field JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE planning_item ADD description LONGTEXT NOT NULL, ADD location VARCHAR(255) NOT NULL, CHANGE summary title VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE planning_log');
        $this->addSql('ALTER TABLE planning_item ADD summary VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP title, DROP description, DROP location');
    }
}
