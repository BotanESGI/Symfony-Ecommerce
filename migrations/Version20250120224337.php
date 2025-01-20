<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250120224337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE address (id SERIAL NOT NULL, user_id INT NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postal_code VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D4E6F81A76ED395 ON address (user_id)');
        $this->addSql('CREATE TABLE cart (id SERIAL NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BA388B7A76ED395 ON cart (user_id)');
        $this->addSql('CREATE TABLE cart_item (id SERIAL NOT NULL, cart_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F0FE25271AD5CDBF ON cart_item (cart_id)');
        $this->addSql('CREATE INDEX IDX_F0FE25274584665A ON cart_item (product_id)');
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE digital_product (id INT NOT NULL, download_link VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE invoice (id SERIAL NOT NULL, user_id INT NOT NULL, total_amount DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_90651744A76ED395 ON invoice (user_id)');
        $this->addSql('CREATE TABLE order_item (id SERIAL NOT NULL, order_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, price NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_52EA1F098D9F6D38 ON order_item (order_id)');
        $this->addSql('CREATE INDEX IDX_52EA1F094584665A ON order_item (product_id)');
        $this->addSql('CREATE TABLE orders (id SERIAL NOT NULL, user_id INT NOT NULL, total NUMERIC(10, 2) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E52FFDEEA76ED395 ON orders (user_id)');
        $this->addSql('COMMENT ON COLUMN orders.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE physical_product (id INT NOT NULL, weight VARCHAR(255) NOT NULL, dimensions VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE product (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, price DOUBLE PRECISION NOT NULL, image VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, product_type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE product_tag (product_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(product_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_E3A6E39C4584665A ON product_tag (product_id)');
        $this->addSql('CREATE INDEX IDX_E3A6E39CBAD26311 ON product_tag (tag_id)');
        $this->addSql('CREATE TABLE product_category (product_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(product_id, category_id))');
        $this->addSql('CREATE INDEX IDX_CDFC73564584665A ON product_category (product_id)');
        $this->addSql('CREATE INDEX IDX_CDFC735612469DE2 ON product_category (category_id)');
        $this->addSql('CREATE TABLE review (id SERIAL NOT NULL, user_id INT NOT NULL, product_id INT NOT NULL, content TEXT NOT NULL, rating INT NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_794381C6A76ED395 ON review (user_id)');
        $this->addSql('CREATE INDEX IDX_794381C64584665A ON review (product_id)');
        $this->addSql('CREATE TABLE tag (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25271AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25274584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE digital_product ADD CONSTRAINT FK_F3C8DFA7BF396750 FOREIGN KEY (id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F094584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE physical_product ADD CONSTRAINT FK_C7F2AC80BF396750 FOREIGN KEY (id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_tag ADD CONSTRAINT FK_E3A6E39C4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_tag ADD CONSTRAINT FK_E3A6E39CBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_category ADD CONSTRAINT FK_CDFC73564584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_category ADD CONSTRAINT FK_CDFC735612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C64584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE address DROP CONSTRAINT FK_D4E6F81A76ED395');
        $this->addSql('ALTER TABLE cart DROP CONSTRAINT FK_BA388B7A76ED395');
        $this->addSql('ALTER TABLE cart_item DROP CONSTRAINT FK_F0FE25271AD5CDBF');
        $this->addSql('ALTER TABLE cart_item DROP CONSTRAINT FK_F0FE25274584665A');
        $this->addSql('ALTER TABLE digital_product DROP CONSTRAINT FK_F3C8DFA7BF396750');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_90651744A76ED395');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F094584665A');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE physical_product DROP CONSTRAINT FK_C7F2AC80BF396750');
        $this->addSql('ALTER TABLE product_tag DROP CONSTRAINT FK_E3A6E39C4584665A');
        $this->addSql('ALTER TABLE product_tag DROP CONSTRAINT FK_E3A6E39CBAD26311');
        $this->addSql('ALTER TABLE product_category DROP CONSTRAINT FK_CDFC73564584665A');
        $this->addSql('ALTER TABLE product_category DROP CONSTRAINT FK_CDFC735612469DE2');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C64584665A');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE cart_item');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE digital_product');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE physical_product');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_tag');
        $this->addSql('DROP TABLE product_category');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE "user"');
    }
}
