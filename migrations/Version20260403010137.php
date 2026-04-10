<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260403010137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE repair_line_employee (id INT AUTO_INCREMENT NOT NULL, role VARCHAR(255) DEFAULT NULL, hours DOUBLE PRECISION DEFAULT NULL, cost DOUBLE PRECISION NOT NULL, repair_line_id INT NOT NULL, employee_id INT NOT NULL, INDEX IDX_D8035DECD3C020D1 (repair_line_id), INDEX IDX_D8035DEC8C03F15C (employee_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE repair_line_employee ADD CONSTRAINT FK_D8035DECD3C020D1 FOREIGN KEY (repair_line_id) REFERENCES repair_line (id)');
        $this->addSql('ALTER TABLE repair_line_employee ADD CONSTRAINT FK_D8035DEC8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE repair_line_employee DROP FOREIGN KEY FK_D8035DECD3C020D1');
        $this->addSql('ALTER TABLE repair_line_employee DROP FOREIGN KEY FK_D8035DEC8C03F15C');
        $this->addSql('DROP TABLE repair_line_employee');
    }
}
