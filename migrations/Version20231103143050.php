<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231103143050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE garden (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(128) NOT NULL, description VARCHAR(1000) NOT NULL, location VARCHAR(255) NOT NULL, postal_code VARCHAR(10) NOT NULL, city VARCHAR(64) NOT NULL, lat DOUBLE PRECISION NOT NULL, lon DOUBLE PRECISION NOT NULL, checked VARCHAR(30) NOT NULL, water TINYINT(1) NOT NULL, tool TINYINT(1) NOT NULL, shed TINYINT(1) NOT NULL, state VARCHAR(128) NOT NULL, surface INT NOT NULL, phone_access TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE garden');
    }
}
