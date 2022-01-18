<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220118153445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD discr VARCHAR(255) NOT NULL, ADD password VARCHAR(32) DEFAULT NULL, ADD roles JSON DEFAULT NULL, CHANGE avatar avatar VARCHAR(32) DEFAULT NULL, CHANGE discord_id discord_id VARCHAR(25) DEFAULT NULL, CHANGE secret_key secret_key VARCHAR(32) DEFAULT NULL');
        $this->addSql('UPDATE user set discr =\'discord\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP discr, DROP password, DROP roles, CHANGE avatar avatar VARCHAR(32) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE discord_id discord_id VARCHAR(25) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE secret_key secret_key VARCHAR(32) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
