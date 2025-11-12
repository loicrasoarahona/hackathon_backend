<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251112081336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE culture (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE culture_city (culture_id INT NOT NULL, city_id INT NOT NULL, INDEX IDX_BCDAB52CB108249D (culture_id), INDEX IDX_BCDAB52C8BAC62AF (city_id), PRIMARY KEY(culture_id, city_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE culture_photo (id INT AUTO_INCREMENT NOT NULL, culture_id INT NOT NULL, filename VARCHAR(255) NOT NULL, INDEX IDX_74A9DF9B108249D (culture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE place_event (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, place_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_92D811848BAC62AF (city_id), INDEX IDX_92D81184DA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE place_event_photo (id INT AUTO_INCREMENT NOT NULL, place_event_id INT NOT NULL, filename VARCHAR(255) NOT NULL, INDEX IDX_5B597D905BAC0F08 (place_event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE culture_city ADD CONSTRAINT FK_BCDAB52CB108249D FOREIGN KEY (culture_id) REFERENCES culture (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE culture_city ADD CONSTRAINT FK_BCDAB52C8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE culture_photo ADD CONSTRAINT FK_74A9DF9B108249D FOREIGN KEY (culture_id) REFERENCES culture (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place_event ADD CONSTRAINT FK_92D811848BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place_event ADD CONSTRAINT FK_92D81184DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place_event_photo ADD CONSTRAINT FK_5B597D905BAC0F08 FOREIGN KEY (place_event_id) REFERENCES place_event (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE culture_city DROP FOREIGN KEY FK_BCDAB52CB108249D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE culture_city DROP FOREIGN KEY FK_BCDAB52C8BAC62AF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE culture_photo DROP FOREIGN KEY FK_74A9DF9B108249D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place_event DROP FOREIGN KEY FK_92D811848BAC62AF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place_event DROP FOREIGN KEY FK_92D81184DA6A219
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place_event_photo DROP FOREIGN KEY FK_5B597D905BAC0F08
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE culture
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE culture_city
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE culture_photo
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE place_event
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE place_event_photo
        SQL);
    }
}
