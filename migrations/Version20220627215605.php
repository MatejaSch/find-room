<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220627215605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD cancelled_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955187B2D12 FOREIGN KEY (cancelled_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_42C84955187B2D12 ON reservation (cancelled_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955187B2D12');
        $this->addSql('DROP INDEX IDX_42C84955187B2D12 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP cancelled_by_id');
    }
}
