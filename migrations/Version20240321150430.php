<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240321150430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_details DROP INDEX UNIQ_845CA2C1DD4481AD, ADD INDEX IDX_845CA2C1DD4481AD (id_order_id)');
        $this->addSql('ALTER TABLE order_details ADD quantity VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_details DROP INDEX IDX_845CA2C1DD4481AD, ADD UNIQUE INDEX UNIQ_845CA2C1DD4481AD (id_order_id)');
        $this->addSql('ALTER TABLE order_details DROP quantity');
    }
}
