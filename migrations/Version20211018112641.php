<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211018112641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE role_allow_section (role_id VARCHAR(25) NOT NULL, SectionId BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(SectionId)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', server_id VARCHAR(25) NOT NULL, category_id VARCHAR(25) NOT NULL, creator_id VARCHAR(25) DEFAULT NULL, creator_name VARCHAR(25) NOT NULL, date DATETIME NOT NULL, name VARCHAR(25) NOT NULL, emoji VARCHAR(5) NOT NULL, visibility VARCHAR(10) NOT NULL, role_id VARCHAR(25) NOT NULL, announce_channel_id VARCHAR(25) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', work_category_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(25) NOT NULL, description VARCHAR(255) NOT NULL, creation_date DATETIME NOT NULL, due_date DATETIME NOT NULL, category_id VARCHAR(25) NOT NULL, server_id VARCHAR(25) NOT NULL, INDEX IDX_534E6880D877D21 (work_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_category (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE role_allow_section ADD CONSTRAINT FK_AC411DA6C17A9B23 FOREIGN KEY (SectionId) REFERENCES section (id)');
        $this->addSql('ALTER TABLE work ADD CONSTRAINT FK_534E6880D877D21 FOREIGN KEY (work_category_id) REFERENCES work_category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role_allow_section DROP FOREIGN KEY FK_AC411DA6C17A9B23');
        $this->addSql('ALTER TABLE work DROP FOREIGN KEY FK_534E6880D877D21');
        $this->addSql('DROP TABLE role_allow_section');
        $this->addSql('DROP TABLE section');
        $this->addSql('DROP TABLE work');
        $this->addSql('DROP TABLE work_category');
    }
}
