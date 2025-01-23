<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250123150613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE invoice ADD pdf_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD invoice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE2989F1FD ON orders (invoice_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE2989F1FD');
        $this->addSql('DROP INDEX UNIQ_E52FFDEE2989F1FD');
        $this->addSql('ALTER TABLE orders DROP invoice_id');
        $this->addSql('ALTER TABLE invoice DROP pdf_path');
    }
}
