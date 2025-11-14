<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251114101916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE dialect_page ADD dialect_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dialect_page ADD CONSTRAINT FK_C281A20D60E7C5AA FOREIGN KEY (dialect_id) REFERENCES dialect (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C281A20D60E7C5AA ON dialect_page (dialect_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE dialect_page DROP FOREIGN KEY FK_C281A20D60E7C5AA
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_C281A20D60E7C5AA ON dialect_page
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dialect_page DROP dialect_id
        SQL);
    }
}
