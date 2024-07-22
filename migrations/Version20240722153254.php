<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240722153254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE employee (id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', first_name VARCHAR(64) NOT NULL, last_name VARCHAR(64) NOT NULL, UNIQUE INDEX UNIQ_5D9F75A1D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE working_time (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, date DATE NOT NULL, INDEX IDX_31EE2ABF8C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE working_time ADD CONSTRAINT FK_31EE2ABF8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE working_time DROP FOREIGN KEY FK_31EE2ABF8C03F15C');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE working_time');
    }
}
