<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251114095955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE dialect (id INT AUTO_INCREMENT NOT NULL, province_id INT NOT NULL, UNIQUE INDEX UNIQ_4F687BD6E946114A (province_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE dialect_page (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dialect ADD CONSTRAINT FK_4F687BD6E946114A FOREIGN KEY (province_id) REFERENCES province (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE dialect DROP FOREIGN KEY FK_4F687BD6E946114A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE dialect
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE dialect_page
        SQL);
    }
}
