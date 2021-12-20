<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211219191432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE guild_settings (GuildId VARCHAR(25) NOT NULL, announce_channel_id VARCHAR(25) DEFAULT NULL, section_message_id VARCHAR(25) DEFAULT NULL, work_channel_id VARCHAR(25) DEFAULT NULL, PRIMARY KEY(GuildId)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_allow_section (role_id VARCHAR(25) NOT NULL, SectionId VARCHAR(255) NOT NULL, INDEX IDX_AC411DA6C17A9B23 (SectionId), PRIMARY KEY(SectionId, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section (id VARCHAR(255) NOT NULL, server_id VARCHAR(25) DEFAULT NULL, category_id VARCHAR(25) NOT NULL, creator_id VARCHAR(25) DEFAULT NULL, creator_name VARCHAR(25) NOT NULL, date DATETIME NOT NULL, name VARCHAR(25) NOT NULL, emoji LONGBLOB NOT NULL, visibility VARCHAR(10) NOT NULL, role_id VARCHAR(25) NOT NULL, announce_channel_id VARCHAR(25) DEFAULT NULL, UNIQUE INDEX UNIQ_2D737AEFD60322AC (role_id), INDEX IDX_2D737AEF1844E6B7 (server_id), UNIQUE INDEX name_guild_unique (server_id, name), UNIQUE INDEX category_guild_unique (server_id, category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(25) NOT NULL, email VARCHAR(25) NOT NULL, avatar VARCHAR(32) NOT NULL, discord_id VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work (id VARCHAR(255) NOT NULL, work_category_id VARCHAR(255) DEFAULT NULL, name VARCHAR(25) NOT NULL, description VARCHAR(255) NOT NULL, creation_date DATETIME NOT NULL, due_date DATETIME NOT NULL, category_id VARCHAR(25) NOT NULL, server_id VARCHAR(25) NOT NULL, INDEX IDX_534E6880D877D21 (work_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_category (id VARCHAR(255) NOT NULL, name VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE role_allow_section ADD CONSTRAINT FK_AC411DA6C17A9B23 FOREIGN KEY (SectionId) REFERENCES section (id)');
        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_2D737AEF1844E6B7 FOREIGN KEY (server_id) REFERENCES guild_settings (GuildId)');
        $this->addSql('ALTER TABLE work ADD CONSTRAINT FK_534E6880D877D21 FOREIGN KEY (work_category_id) REFERENCES work_category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_2D737AEF1844E6B7');
        $this->addSql('ALTER TABLE role_allow_section DROP FOREIGN KEY FK_AC411DA6C17A9B23');
        $this->addSql('ALTER TABLE work DROP FOREIGN KEY FK_534E6880D877D21');
        $this->addSql('DROP TABLE guild_settings');
        $this->addSql('DROP TABLE role_allow_section');
        $this->addSql('DROP TABLE section');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE work');
        $this->addSql('DROP TABLE work_category');
    }
}
