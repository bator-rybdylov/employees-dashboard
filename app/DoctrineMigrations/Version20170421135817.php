<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170421135817 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE employees_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE departments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE employees (id INT NOT NULL, department_id INT DEFAULT NULL, firstName VARCHAR(255) NOT NULL, lastName VARCHAR(255) NOT NULL, patronymic VARCHAR(255) DEFAULT NULL, birthday DATE DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phoneNumber VARCHAR(255) DEFAULT NULL, hireDate DATE DEFAULT NULL, retireDate DATE DEFAULT NULL, position VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BA82C300E7927C74 ON employees (email)');
        $this->addSql('CREATE INDEX IDX_BA82C300AE80F5DF ON employees (department_id)');
        $this->addSql('CREATE TABLE departments (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE employees ADD CONSTRAINT FK_BA82C300AE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE employees DROP CONSTRAINT FK_BA82C300AE80F5DF');
        $this->addSql('DROP SEQUENCE employees_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE departments_id_seq CASCADE');
        $this->addSql('DROP TABLE employees');
        $this->addSql('DROP TABLE departments');
    }
}
