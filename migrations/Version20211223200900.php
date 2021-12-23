<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211223200900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE planning_log CHANGE title_previous title_previous VARCHAR(255) DEFAULT NULL, CHANGE description_previous description_previous LONGTEXT DEFAULT NULL, CHANGE date_start_previous date_start_previous DATETIME DEFAULT NULL, CHANGE date_end_previous date_end_previous DATETIME DEFAULT NULL, CHANGE teacher_previous teacher_previous VARCHAR(255) DEFAULT NULL, CHANGE location_previous location_previous VARCHAR(255) DEFAULT NULL, CHANGE title_next title_next VARCHAR(255) DEFAULT NULL, CHANGE description_next description_next LONGTEXT DEFAULT NULL, CHANGE date_start_next date_start_next DATETIME DEFAULT NULL, CHANGE date_end_next date_end_next DATETIME DEFAULT NULL, CHANGE teacher_next teacher_next VARCHAR(255) DEFAULT NULL, CHANGE location_next location_next VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE planning_log CHANGE title_previous title_previous VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description_previous description_previous LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE date_start_previous date_start_previous DATETIME NOT NULL, CHANGE date_end_previous date_end_previous DATETIME NOT NULL, CHANGE teacher_previous teacher_previous VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE location_previous location_previous VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE title_next title_next VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description_next description_next LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE date_start_next date_start_next DATETIME NOT NULL, CHANGE date_end_next date_end_next DATETIME NOT NULL, CHANGE teacher_next teacher_next VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE location_next location_next VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
