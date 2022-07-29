<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220727160207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, url VARCHAR(255) NOT NULL, username VARCHAR(50) NOT NULL, password VARCHAR(70) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_smartphone (customer_id INT NOT NULL, smartphone_id INT NOT NULL, INDEX IDX_DB797AAC9395C3F3 (customer_id), INDEX IDX_DB797AAC2E4F4908 (smartphone_id), PRIMARY KEY(customer_id, smartphone_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE smartphone (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, color VARCHAR(20) NOT NULL, price DOUBLE PRECISION NOT NULL, year DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D6499395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_smartphone ADD CONSTRAINT FK_DB797AAC9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_smartphone ADD CONSTRAINT FK_DB797AAC2E4F4908 FOREIGN KEY (smartphone_id) REFERENCES smartphone (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_smartphone DROP FOREIGN KEY FK_DB797AAC9395C3F3');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499395C3F3');
        $this->addSql('ALTER TABLE customer_smartphone DROP FOREIGN KEY FK_DB797AAC2E4F4908');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE customer_smartphone');
        $this->addSql('DROP TABLE smartphone');
        $this->addSql('DROP TABLE user');
    }
}
