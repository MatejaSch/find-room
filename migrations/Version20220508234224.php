<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220508234224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bed (id INT AUTO_INCREMENT NOT NULL, bed_type VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_bed (id INT AUTO_INCREMENT NOT NULL, room_id INT NOT NULL, bed_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_E4C141E254177093 (room_id), INDEX IDX_E4C141E288688BB9 (bed_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE room_bed ADD CONSTRAINT FK_E4C141E254177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE room_bed ADD CONSTRAINT FK_E4C141E288688BB9 FOREIGN KEY (bed_id) REFERENCES bed (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room_bed DROP FOREIGN KEY FK_E4C141E288688BB9');
        $this->addSql('DROP TABLE bed');
        $this->addSql('DROP TABLE room_bed');
    }
}
