<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260118211059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE productos CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE reviews CHANGE rating rating INT NOT NULL, CHANGE reviews_text reviews_text LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE reviews RENAME INDEX id_usuario TO IDX_6970EB0FFCF8192D');
        $this->addSql('ALTER TABLE reviews RENAME INDEX id_producto TO IDX_6970EB0FF760EA80');
        $this->addSql('DROP INDEX email ON usuarios');
        $this->addSql('DROP INDEX username ON usuarios');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE productos CHANGE description description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE reviews CHANGE rating rating TINYINT NOT NULL, CHANGE reviews_text reviews_text TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE reviews RENAME INDEX idx_6970eb0ff760ea80 TO id_producto');
        $this->addSql('ALTER TABLE reviews RENAME INDEX idx_6970eb0ffcf8192d TO id_usuario');
        $this->addSql('CREATE UNIQUE INDEX email ON usuarios (email)');
        $this->addSql('CREATE UNIQUE INDEX username ON usuarios (username)');
    }
}
