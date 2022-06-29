<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220620182058 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offer (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(50) NOT NULL, price_per_night DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offer_image (id INT AUTO_INCREMENT NOT NULL, offer_id INT NOT NULL, image_name VARCHAR(255) NOT NULL, INDEX IDX_461079B653C674EE (offer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offer_image ADD CONSTRAINT FK_461079B653C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
        $this->addSql('ALTER TABLE room ADD offer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B53C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
        $this->addSql('CREATE INDEX IDX_729F519B53C674EE ON room (offer_id)');
        $this->addSql('ALTER TABLE room_image RENAME INDEX idx_a15178ab54177093 TO IDX_8F81A5F454177093');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offer_image DROP FOREIGN KEY FK_461079B653C674EE');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B53C674EE');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE offer_image');
        $this->addSql('DROP INDEX IDX_729F519B53C674EE ON room');
        $this->addSql('ALTER TABLE room DROP offer_id');
        $this->addSql('ALTER TABLE room_image RENAME INDEX idx_8f81a5f454177093 TO IDX_A15178AB54177093');
    }
}
