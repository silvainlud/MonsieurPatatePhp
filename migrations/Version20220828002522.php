<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220828002522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_push_subscriber (endpoint VARCHAR(255) NOT NULL, user_id INT DEFAULT NULL, key_p256dh VARCHAR(100) NOT NULL, key_auth VARCHAR(30) NOT NULL, INDEX IDX_213378ADA76ED395 (user_id), PRIMARY KEY(endpoint)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_push_subscriber ADD CONSTRAINT FK_213378ADA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_push_subscriber');
        $this->addSql('ALTER TABLE allow_user_command CHANGE member_id member_id VARCHAR(25) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE guild_settings CHANGE GuildId GuildId VARCHAR(25) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE announce_channel_id announce_channel_id VARCHAR(25) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE section_message_id section_message_id VARCHAR(25) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE work_announce_channel_id work_announce_channel_id VARCHAR(25) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE planning_notify_channel_id planning_notify_channel_id VARCHAR(25) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE work_recall_channel_id work_recall_channel_id VARCHAR(25) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE planning_item CHANGE id id VARCHAR(64) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE title title VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE teacher teacher VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE location location VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE planning_log CHANGE action_type action_type VARCHAR(10) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE planning_uuid planning_uuid VARCHAR(64) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE title_previous title_previous VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description_previous description_previous LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE teacher_previous teacher_previous VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE location_previous location_previous VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE title_next title_next VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description_next description_next LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE teacher_next teacher_next VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE location_next location_next VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE role_allow_section CHANGE role_id role_id VARCHAR(25) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE SectionId SectionId VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE section CHANGE id id VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE server_id server_id VARCHAR(25) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE category_id category_id VARCHAR(25) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE creator_id creator_id VARCHAR(25) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE creator_name creator_name VARCHAR(25) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE name name VARCHAR(25) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE visibility visibility VARCHAR(10) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE role_id role_id VARCHAR(25) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE announce_channel_id announce_channel_id VARCHAR(25) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(25) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(25) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE discr discr VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE avatar avatar VARCHAR(32) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE discord_id discord_id VARCHAR(25) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE secret_key secret_key VARCHAR(32) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(62) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE work CHANGE server_id server_id VARCHAR(25) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE name name VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE message_id message_id VARCHAR(40) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE work_category CHANGE name name VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
