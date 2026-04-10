<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260403012004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE repair_line_part (id INT AUTO_INCREMENT NOT NULL, repair_line_id INT NOT NULL, part_id INT DEFAULT NULL, INDEX IDX_832F6106D3C020D1 (repair_line_id), INDEX IDX_832F61064CE34BEC (part_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE repair_line_part ADD CONSTRAINT FK_832F6106D3C020D1 FOREIGN KEY (repair_line_id) REFERENCES repair_line (id)');
        $this->addSql('ALTER TABLE repair_line_part ADD CONSTRAINT FK_832F61064CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE repair_line_part DROP FOREIGN KEY FK_832F6106D3C020D1');
        $this->addSql('ALTER TABLE repair_line_part DROP FOREIGN KEY FK_832F61064CE34BEC');
        $this->addSql('DROP TABLE repair_line_part');
    }
}
