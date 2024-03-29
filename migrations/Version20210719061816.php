<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210719061816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE result_campaign_user ADD destinataire_id INT NOT NULL');
        $this->addSql('ALTER TABLE result_campaign_user ADD CONSTRAINT FK_CF7FFA8A4F84F6E FOREIGN KEY (destinataire_id) REFERENCES destinataire (id)');
        $this->addSql('CREATE INDEX IDX_CF7FFA8A4F84F6E ON result_campaign_user (destinataire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE result_campaign_user DROP FOREIGN KEY FK_CF7FFA8A4F84F6E');
        $this->addSql('DROP INDEX IDX_CF7FFA8A4F84F6E ON result_campaign_user');
        $this->addSql('ALTER TABLE result_campaign_user DROP destinataire_id');
    }
}
