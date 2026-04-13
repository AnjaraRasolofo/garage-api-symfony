<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260413144811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, email VARCHAR(150) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, code VARCHAR(50) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE employee (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, function VARCHAR(255) DEFAULT NULL, hiring_date DATE DEFAULT NULL, salary DOUBLE PRECISION DEFAULT NULL, status VARCHAR(50) DEFAULT NULL, number VARCHAR(50) DEFAULT NULL, birth_date DATE DEFAULT NULL, department_id INT DEFAULT NULL, INDEX IDX_5D9F75A1AE80F5DF (department_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, invoice_number VARCHAR(50) DEFAULT NULL, invoice_date DATE NOT NULL, status VARCHAR(50) DEFAULT NULL, total DOUBLE PRECISION DEFAULT NULL, customer_id INT DEFAULT NULL, repair_id INT NOT NULL, INDEX IDX_906517449395C3F3 (customer_id), UNIQUE INDEX UNIQ_9065174443833CFF (repair_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE invoice_item (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, quantity DOUBLE PRECISION NOT NULL, unit_price DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION NOT NULL, invoice_id INT DEFAULT NULL, INDEX IDX_1DDE477B2989F1FD (invoice_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE part (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, reference VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, quantity DOUBLE PRECISION NOT NULL, min_quantity DOUBLE PRECISION DEFAULT NULL, provider VARCHAR(255) DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, category_id INT DEFAULT NULL, INDEX IDX_490F70C612469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE repair (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, status VARCHAR(50) NOT NULL, vehicle_id INT DEFAULT NULL, INDEX IDX_8EE43421545317D1 (vehicle_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE repair_line (id INT AUTO_INCREMENT NOT NULL, custom_title VARCHAR(255) DEFAULT NULL, labor_cost DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION NOT NULL, repair_id INT DEFAULT NULL, work_task_id INT DEFAULT NULL, INDEX IDX_432601643833CFF (repair_id), INDEX IDX_4326016587DB9A4 (work_task_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE repair_line_employee (id INT AUTO_INCREMENT NOT NULL, role VARCHAR(255) DEFAULT NULL, hours DOUBLE PRECISION DEFAULT NULL, cost DOUBLE PRECISION NOT NULL, repair_line_id INT NOT NULL, employee_id INT NOT NULL, INDEX IDX_D8035DECD3C020D1 (repair_line_id), INDEX IDX_D8035DEC8C03F15C (employee_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE repair_line_part (id INT AUTO_INCREMENT NOT NULL, quantity DOUBLE PRECISION NOT NULL, price DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION NOT NULL, repair_line_id INT NOT NULL, part_id INT DEFAULT NULL, INDEX IDX_832F6106D3C020D1 (repair_line_id), INDEX IDX_832F61064CE34BEC (part_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE stock_movement (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, quantity DOUBLE PRECISION NOT NULL, reason VARCHAR(255) DEFAULT NULL, movement_date DATE DEFAULT NULL, part_id INT NOT NULL, INDEX IDX_BB1BC1B54CE34BEC (part_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE vehicle (id INT AUTO_INCREMENT NOT NULL, brand VARCHAR(100) NOT NULL, model VARCHAR(100) NOT NULL, number VARCHAR(50) NOT NULL, year INT NOT NULL, color VARCHAR(50) DEFAULT NULL, fuel_type VARCHAR(50) DEFAULT NULL, mileage INT DEFAULT NULL, vin VARCHAR(50) DEFAULT NULL, engine_number VARCHAR(50) DEFAULT NULL, insurance_expiry_date DATE DEFAULT NULL, last_service_date DATE DEFAULT NULL, status VARCHAR(50) DEFAULT NULL, customer_id INT NOT NULL, INDEX IDX_1B80E4869395C3F3 (customer_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE work_task_template (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, default_labor_cost DOUBLE PRECISION NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517449395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174443833CFF FOREIGN KEY (repair_id) REFERENCES repair (id)');
        $this->addSql('ALTER TABLE invoice_item ADD CONSTRAINT FK_1DDE477B2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('ALTER TABLE part ADD CONSTRAINT FK_490F70C612469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE repair ADD CONSTRAINT FK_8EE43421545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE repair_line ADD CONSTRAINT FK_432601643833CFF FOREIGN KEY (repair_id) REFERENCES repair (id)');
        $this->addSql('ALTER TABLE repair_line ADD CONSTRAINT FK_4326016587DB9A4 FOREIGN KEY (work_task_id) REFERENCES work_task_template (id)');
        $this->addSql('ALTER TABLE repair_line_employee ADD CONSTRAINT FK_D8035DECD3C020D1 FOREIGN KEY (repair_line_id) REFERENCES repair_line (id)');
        $this->addSql('ALTER TABLE repair_line_employee ADD CONSTRAINT FK_D8035DEC8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE repair_line_part ADD CONSTRAINT FK_832F6106D3C020D1 FOREIGN KEY (repair_line_id) REFERENCES repair_line (id)');
        $this->addSql('ALTER TABLE repair_line_part ADD CONSTRAINT FK_832F61064CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
        $this->addSql('ALTER TABLE stock_movement ADD CONSTRAINT FK_BB1BC1B54CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
        $this->addSql('ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E4869395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1AE80F5DF');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517449395C3F3');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_9065174443833CFF');
        $this->addSql('ALTER TABLE invoice_item DROP FOREIGN KEY FK_1DDE477B2989F1FD');
        $this->addSql('ALTER TABLE part DROP FOREIGN KEY FK_490F70C612469DE2');
        $this->addSql('ALTER TABLE repair DROP FOREIGN KEY FK_8EE43421545317D1');
        $this->addSql('ALTER TABLE repair_line DROP FOREIGN KEY FK_432601643833CFF');
        $this->addSql('ALTER TABLE repair_line DROP FOREIGN KEY FK_4326016587DB9A4');
        $this->addSql('ALTER TABLE repair_line_employee DROP FOREIGN KEY FK_D8035DECD3C020D1');
        $this->addSql('ALTER TABLE repair_line_employee DROP FOREIGN KEY FK_D8035DEC8C03F15C');
        $this->addSql('ALTER TABLE repair_line_part DROP FOREIGN KEY FK_832F6106D3C020D1');
        $this->addSql('ALTER TABLE repair_line_part DROP FOREIGN KEY FK_832F61064CE34BEC');
        $this->addSql('ALTER TABLE stock_movement DROP FOREIGN KEY FK_BB1BC1B54CE34BEC');
        $this->addSql('ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E4869395C3F3');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE invoice_item');
        $this->addSql('DROP TABLE part');
        $this->addSql('DROP TABLE repair');
        $this->addSql('DROP TABLE repair_line');
        $this->addSql('DROP TABLE repair_line_employee');
        $this->addSql('DROP TABLE repair_line_part');
        $this->addSql('DROP TABLE stock_movement');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vehicle');
        $this->addSql('DROP TABLE work_task_template');
    }
}
