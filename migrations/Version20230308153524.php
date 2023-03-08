<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308153524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_cities (user_id INT NOT NULL, cities_id INT NOT NULL, INDEX IDX_BC0FAA56A76ED395 (user_id), INDEX IDX_BC0FAA56CAC75398 (cities_id), PRIMARY KEY(user_id, cities_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_cities ADD CONSTRAINT FK_BC0FAA56A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_cities ADD CONSTRAINT FK_BC0FAA56CAC75398 FOREIGN KEY (cities_id) REFERENCES cities (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_cities DROP FOREIGN KEY FK_BC0FAA56A76ED395');
        $this->addSql('ALTER TABLE user_cities DROP FOREIGN KEY FK_BC0FAA56CAC75398');
        $this->addSql('DROP TABLE user_cities');
    }
}
