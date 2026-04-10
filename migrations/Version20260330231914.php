<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260330231914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stock_movement (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, quantity DOUBLE PRECISION NOT NULL, reason VARCHAR(255) DEFAULT NULL, movement_date DATE DEFAULT NULL, part_id INT NOT NULL, INDEX IDX_BB1BC1B54CE34BEC (part_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE stock_movement ADD CONSTRAINT FK_BB1BC1B54CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_movement DROP FOREIGN KEY FK_BB1BC1B54CE34BEC');
        $this->addSql('DROP TABLE stock_movement');
    }
}
