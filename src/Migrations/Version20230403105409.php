<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230403105409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_plus_return_request ADD shipping_method_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_plus_return_request ADD CONSTRAINT FK_164D475F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES sylius_shipping_method (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_164D475F7D6850 ON sylius_plus_return_request (shipping_method_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_plus_return_request DROP FOREIGN KEY FK_164D475F7D6850');
        $this->addSql('DROP INDEX UNIQ_164D475F7D6850 ON sylius_plus_return_request');
        $this->addSql('ALTER TABLE sylius_plus_return_request DROP shipping_method_id');
    }
}
