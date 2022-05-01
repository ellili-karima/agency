<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220430234607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bien ADD employeur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bien ADD CONSTRAINT FK_45EDC3865D7C53EC FOREIGN KEY (employeur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_45EDC3865D7C53EC ON bien (employeur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bien DROP FOREIGN KEY FK_45EDC3865D7C53EC');
        $this->addSql('DROP INDEX IDX_45EDC3865D7C53EC ON bien');
        $this->addSql('ALTER TABLE bien DROP employeur_id');
    }
}
