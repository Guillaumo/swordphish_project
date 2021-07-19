<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210716152131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campagne_destinataire (campagne_id INT NOT NULL, destinataire_id INT NOT NULL, INDEX IDX_BAD1F48516227374 (campagne_id), INDEX IDX_BAD1F485A4F84F6E (destinataire_id), PRIMARY KEY(campagne_id, destinataire_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE campagne_destinataire ADD CONSTRAINT FK_BAD1F48516227374 FOREIGN KEY (campagne_id) REFERENCES campagne (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE campagne_destinataire ADD CONSTRAINT FK_BAD1F485A4F84F6E FOREIGN KEY (destinataire_id) REFERENCES destinataire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE result_campaign_user ADD campagne_id INT NOT NULL');
        $this->addSql('ALTER TABLE result_campaign_user ADD CONSTRAINT FK_CF7FFA816227374 FOREIGN KEY (campagne_id) REFERENCES campagne (id)');
        $this->addSql('CREATE INDEX IDX_CF7FFA816227374 ON result_campaign_user (campagne_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE campagne_destinataire');
        $this->addSql('ALTER TABLE result_campaign_user DROP FOREIGN KEY FK_CF7FFA816227374');
        $this->addSql('DROP INDEX IDX_CF7FFA816227374 ON result_campaign_user');
        $this->addSql('ALTER TABLE result_campaign_user DROP campagne_id');
    }
}
