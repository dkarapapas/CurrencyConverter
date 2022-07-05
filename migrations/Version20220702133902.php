<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220702133902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__currency AS SELECT id, currency_identifier, description, currency_icon FROM currency');
        $this->addSql('DROP TABLE currency');
        $this->addSql('CREATE TABLE currency (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, currency_identifier VARCHAR(3) NOT NULL, description VARCHAR(30) NOT NULL, currency_icon VARCHAR(10) NOT NULL)');
        $this->addSql('INSERT INTO currency (id, currency_identifier, description, currency_icon) SELECT id, currency_identifier, description, currency_icon FROM __temp__currency');
        $this->addSql('DROP TABLE __temp__currency');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE currency ADD COLUMN value NUMERIC(7, 4) NOT NULL');
    }
}
