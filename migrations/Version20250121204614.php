<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250121204614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE physical_product ADD characteristics JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE physical_product DROP weight');
        $this->addSql('ALTER TABLE physical_product DROP dimensions');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE physical_product ADD weight VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE physical_product ADD dimensions VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE physical_product DROP characteristics');
    }
}
