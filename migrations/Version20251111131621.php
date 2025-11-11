<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251111131621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, city_id INT NOT NULL, place_id INT DEFAULT NULL, INDEX IDX_5A8A6C8DA76ED395 (user_id), INDEX IDX_5A8A6C8D8BAC62AF (city_id), INDEX IDX_5A8A6C8DDA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE post_user (post_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_44C6B1424B89032C (post_id), INDEX IDX_44C6B142A76ED395 (user_id), PRIMARY KEY(post_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE post_photo (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, filename VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_83AC08F74B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE post_photo_post_photo_category (post_photo_id INT NOT NULL, post_photo_category_id INT NOT NULL, INDEX IDX_27B9080EC173C52D (post_photo_id), INDEX IDX_27B9080E2A911F17 (post_photo_category_id), PRIMARY KEY(post_photo_id, post_photo_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE post_photo_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_product_category (product_id INT NOT NULL, product_category_id INT NOT NULL, INDEX IDX_437017AA4584665A (product_id), INDEX IDX_437017AABE6903FD (product_category_id), PRIMARY KEY(product_id, product_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_city (product_id INT NOT NULL, city_id INT NOT NULL, INDEX IDX_A0B320954584665A (product_id), INDEX IDX_A0B320958BAC62AF (city_id), PRIMARY KEY(product_id, city_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DDA6A219 FOREIGN KEY (place_id) REFERENCES place (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post_user ADD CONSTRAINT FK_44C6B1424B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post_user ADD CONSTRAINT FK_44C6B142A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post_photo ADD CONSTRAINT FK_83AC08F74B89032C FOREIGN KEY (post_id) REFERENCES post (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post_photo_post_photo_category ADD CONSTRAINT FK_27B9080EC173C52D FOREIGN KEY (post_photo_id) REFERENCES post_photo (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post_photo_post_photo_category ADD CONSTRAINT FK_27B9080E2A911F17 FOREIGN KEY (post_photo_category_id) REFERENCES post_photo_category (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_product_category ADD CONSTRAINT FK_437017AA4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_product_category ADD CONSTRAINT FK_437017AABE6903FD FOREIGN KEY (product_category_id) REFERENCES product_category (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_city ADD CONSTRAINT FK_A0B320954584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_city ADD CONSTRAINT FK_A0B320958BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D8BAC62AF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DDA6A219
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post_user DROP FOREIGN KEY FK_44C6B1424B89032C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post_user DROP FOREIGN KEY FK_44C6B142A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post_photo DROP FOREIGN KEY FK_83AC08F74B89032C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post_photo_post_photo_category DROP FOREIGN KEY FK_27B9080EC173C52D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post_photo_post_photo_category DROP FOREIGN KEY FK_27B9080E2A911F17
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_product_category DROP FOREIGN KEY FK_437017AA4584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_product_category DROP FOREIGN KEY FK_437017AABE6903FD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_city DROP FOREIGN KEY FK_A0B320954584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_city DROP FOREIGN KEY FK_A0B320958BAC62AF
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE post
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE post_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE post_photo
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE post_photo_post_photo_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE post_photo_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_product_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_city
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_category
        SQL);
    }
}
