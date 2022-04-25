<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220425105536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointement (id INT AUTO_INCREMENT NOT NULL, titre_id INT DEFAULT NULL, date DATETIME NOT NULL, email VARCHAR(50) NOT NULL, tel VARCHAR(15) NOT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) NOT NULL, INDEX IDX_BD9991CDD54FAE5E (titre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bien (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(50) NOT NULL, nbrepieces INT NOT NULL, surface DOUBLE PRECISION NOT NULL, prix DOUBLE PRECISION NOT NULL, localisation VARCHAR(100) NOT NULL, type VARCHAR(15) NOT NULL, etage INT NOT NULL, transactiontype VARCHAR(10) NOT NULL, description VARCHAR(100) NOT NULL, dateconstruction DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `option` (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(15) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE optionbien (id INT AUTO_INCREMENT NOT NULL, idbien_id INT DEFAULT NULL, idoption_id INT DEFAULT NULL, INDEX IDX_BEF80A3B6291EA61 (idbien_id), INDEX IDX_BEF80A3BD9DFB49A (idoption_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, bien_id INT DEFAULT NULL, photo VARCHAR(40) NOT NULL, INDEX IDX_14B78418BD95B80F (bien_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointement ADD CONSTRAINT FK_BD9991CDD54FAE5E FOREIGN KEY (titre_id) REFERENCES bien (id)');
        $this->addSql('ALTER TABLE optionbien ADD CONSTRAINT FK_BEF80A3B6291EA61 FOREIGN KEY (idbien_id) REFERENCES bien (id)');
        $this->addSql('ALTER TABLE optionbien ADD CONSTRAINT FK_BEF80A3BD9DFB49A FOREIGN KEY (idoption_id) REFERENCES `option` (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418BD95B80F FOREIGN KEY (bien_id) REFERENCES bien (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointement DROP FOREIGN KEY FK_BD9991CDD54FAE5E');
        $this->addSql('ALTER TABLE optionbien DROP FOREIGN KEY FK_BEF80A3B6291EA61');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418BD95B80F');
        $this->addSql('ALTER TABLE optionbien DROP FOREIGN KEY FK_BEF80A3BD9DFB49A');
        $this->addSql('DROP TABLE appointement');
        $this->addSql('DROP TABLE bien');
        $this->addSql('DROP TABLE `option`');
        $this->addSql('DROP TABLE optionbien');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
