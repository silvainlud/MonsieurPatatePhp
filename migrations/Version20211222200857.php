<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211222200857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work DROP FOREIGN KEY FK_534E6880D877D21');
        $this->addSql('DROP INDEX IDX_534E6880D877D21 ON work');
        $this->addSql('ALTER TABLE work ADD guild VARCHAR(25) NOT NULL, DROP work_category_id, CHANGE server_id server_id VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE work ADD CONSTRAINT FK_534E68801844E6B7 FOREIGN KEY (server_id) REFERENCES guild_settings (GuildId)');
        $this->addSql('CREATE INDEX IDX_534E68801844E6B7 ON work (server_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work DROP FOREIGN KEY FK_534E68801844E6B7');
        $this->addSql('DROP INDEX IDX_534E68801844E6B7 ON work');
        $this->addSql('ALTER TABLE work ADD work_category_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', DROP guild, CHANGE server_id server_id VARCHAR(25) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE work ADD CONSTRAINT FK_534E6880D877D21 FOREIGN KEY (work_category_id) REFERENCES work_category (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_534E6880D877D21 ON work (work_category_id)');
    }
}
