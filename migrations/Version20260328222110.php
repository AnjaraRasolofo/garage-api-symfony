<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260328222110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vehicle (id INT AUTO_INCREMENT NOT NULL, brand VARCHAR(100) NOT NULL, model VARCHAR(100) NOT NULL, number VARCHAR(50) NOT NULL, year INT NOT NULL, color VARCHAR(50) DEFAULT NULL, fuel_type VARCHAR(50) DEFAULT NULL, mileage INT DEFAULT NULL, vin VARCHAR(50) DEFAULT NULL, engine_number VARCHAR(50) DEFAULT NULL, insurance_expiry_date DATE DEFAULT NULL, last_service_date DATE DEFAULT NULL, status VARCHAR(50) DEFAULT NULL, customer_id INT NOT NULL, INDEX IDX_1B80E4869395C3F3 (customer_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E4869395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E4869395C3F3');
        $this->addSql('DROP TABLE vehicle');
    }
}
