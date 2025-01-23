<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250123103857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE digital_product ADD filesize INT DEFAULT NULL');
        $this->addSql('ALTER TABLE digital_product ADD filetype VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE product DROP file_size');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE digital_product DROP filesize');
        $this->addSql('ALTER TABLE digital_product DROP filetype');
        $this->addSql('ALTER TABLE product ADD file_size INT DEFAULT NULL');
    }
}
