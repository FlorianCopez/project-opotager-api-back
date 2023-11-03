<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231103150955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE garden ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE garden ADD CONSTRAINT FK_3C0918EAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3C0918EAA76ED395 ON garden (user_id)');
        $this->addSql('ALTER TABLE picture ADD garden_id INT NOT NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F8939F3B087 FOREIGN KEY (garden_id) REFERENCES garden (id)');
        $this->addSql('CREATE INDEX IDX_16DB4F8939F3B087 ON picture (garden_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F8939F3B087');
        $this->addSql('DROP INDEX IDX_16DB4F8939F3B087 ON picture');
        $this->addSql('ALTER TABLE picture DROP garden_id');
        $this->addSql('ALTER TABLE garden DROP FOREIGN KEY FK_3C0918EAA76ED395');
        $this->addSql('DROP INDEX IDX_3C0918EAA76ED395 ON garden');
        $this->addSql('ALTER TABLE garden DROP user_id');
    }
}
