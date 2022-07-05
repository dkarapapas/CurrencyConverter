<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704173900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('DROP INDEX IDX_555B7C4DA56723E4');
        $this->addSql('DROP INDEX IDX_555B7C4D67D74803');
        $this->addSql('CREATE TEMPORARY TABLE __temp__currency_rate AS SELECT currency_from_id, currency_to_id, rate FROM currency_rate');
        $this->addSql('DROP TABLE currency_rate');
        $this->addSql('CREATE TABLE currency_rate (currency_from_id INTEGER NOT NULL, currency_to_id INTEGER NOT NULL, rate NUMERIC(10, 4) NOT NULL, PRIMARY KEY(currency_from_id, currency_to_id), CONSTRAINT FK_555B7C4DA56723E4 FOREIGN KEY (currency_from_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_555B7C4D67D74803 FOREIGN KEY (currency_to_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO currency_rate (currency_from_id, currency_to_id, rate) SELECT currency_from_id, currency_to_id, rate FROM __temp__currency_rate');
        $this->addSql('DROP TABLE __temp__currency_rate');
        $this->addSql('CREATE INDEX IDX_555B7C4DA56723E4 ON currency_rate (currency_from_id)');
        $this->addSql('CREATE INDEX IDX_555B7C4D67D74803 ON currency_rate (currency_to_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX IDX_555B7C4DA56723E4');
        $this->addSql('DROP INDEX IDX_555B7C4D67D74803');
        $this->addSql('CREATE TEMPORARY TABLE __temp__currency_rate AS SELECT currency_from_id, currency_to_id, rate FROM currency_rate');
        $this->addSql('DROP TABLE currency_rate');
        $this->addSql('CREATE TABLE currency_rate (currency_from_id INTEGER NOT NULL, currency_to_id INTEGER NOT NULL, rate NUMERIC(10, 4) NOT NULL, PRIMARY KEY(currency_from_id, currency_to_id))');
        $this->addSql('INSERT INTO currency_rate (currency_from_id, currency_to_id, rate) SELECT currency_from_id, currency_to_id, rate FROM __temp__currency_rate');
        $this->addSql('DROP TABLE __temp__currency_rate');
        $this->addSql('CREATE INDEX IDX_555B7C4DA56723E4 ON currency_rate (currency_from_id)');
        $this->addSql('CREATE INDEX IDX_555B7C4D67D74803 ON currency_rate (currency_to_id)');
    }
}
