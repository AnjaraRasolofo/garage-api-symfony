<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260403004653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE repair_line (id INT AUTO_INCREMENT NOT NULL, custom_title VARCHAR(255) DEFAULT NULL, labor_cost DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION NOT NULL, repair_id INT DEFAULT NULL, work_task_id INT DEFAULT NULL, INDEX IDX_432601643833CFF (repair_id), INDEX IDX_4326016587DB9A4 (work_task_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE repair_line ADD CONSTRAINT FK_432601643833CFF FOREIGN KEY (repair_id) REFERENCES repair (id)');
        $this->addSql('ALTER TABLE repair_line ADD CONSTRAINT FK_4326016587DB9A4 FOREIGN KEY (work_task_id) REFERENCES work_task_template (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE repair_line DROP FOREIGN KEY FK_432601643833CFF');
        $this->addSql('ALTER TABLE repair_line DROP FOREIGN KEY FK_4326016587DB9A4');
        $this->addSql('DROP TABLE repair_line');
    }
}
