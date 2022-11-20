<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221109164300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, http_method VARCHAR(255) NOT NULL, sending_frequency INT NOT NULL, is_active TINYINT(1) NOT NULL, sending_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', sent_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_reading (id INT AUTO_INCREMENT NOT NULL, notification_id INT NOT NULL, status INT NOT NULL, content JSON NOT NULL, read_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D40476F6EF1A9D84 (notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_receiver (id INT AUTO_INCREMENT NOT NULL, notification_id INT NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_68A8B433EF1A9D84 (notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification_reading ADD CONSTRAINT FK_D40476F6EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
        $this->addSql('ALTER TABLE notification_receiver ADD CONSTRAINT FK_68A8B433EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification_reading DROP FOREIGN KEY FK_D40476F6EF1A9D84');
        $this->addSql('ALTER TABLE notification_receiver DROP FOREIGN KEY FK_68A8B433EF1A9D84');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE notification_reading');
        $this->addSql('DROP TABLE notification_receiver');
    }
}
