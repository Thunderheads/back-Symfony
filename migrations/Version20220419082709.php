<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220419082709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE application (id INT AUTO_INCREMENT NOT NULL, administrateur_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_A45BDDC17EE5403C (administrateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE donnes (id INT AUTO_INCREMENT NOT NULL, application_id INT NOT NULL, date_collect DATETIME NOT NULL, rating DOUBLE PRECISION NOT NULL, vote INT NOT NULL, INDEX IDX_71F3D50D3E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE os (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE responsable (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source (id INT AUTO_INCREMENT NOT NULL, application_id INT NOT NULL, os_id INT NOT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_5F8A7F733E030ACD (application_id), INDEX IDX_5F8A7F733DCA04D1 (os_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC17EE5403C FOREIGN KEY (administrateur_id) REFERENCES responsable (id)');
        $this->addSql('ALTER TABLE donnes ADD CONSTRAINT FK_71F3D50D3E030ACD FOREIGN KEY (application_id) REFERENCES application (id)');
        $this->addSql('ALTER TABLE source ADD CONSTRAINT FK_5F8A7F733E030ACD FOREIGN KEY (application_id) REFERENCES application (id)');
        $this->addSql('ALTER TABLE source ADD CONSTRAINT FK_5F8A7F733DCA04D1 FOREIGN KEY (os_id) REFERENCES os (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE donnes DROP FOREIGN KEY FK_71F3D50D3E030ACD');
        $this->addSql('ALTER TABLE source DROP FOREIGN KEY FK_5F8A7F733E030ACD');
        $this->addSql('ALTER TABLE source DROP FOREIGN KEY FK_5F8A7F733DCA04D1');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC17EE5403C');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE donnes');
        $this->addSql('DROP TABLE os');
        $this->addSql('DROP TABLE responsable');
        $this->addSql('DROP TABLE source');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
