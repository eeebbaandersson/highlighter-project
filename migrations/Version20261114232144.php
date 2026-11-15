<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261114232144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category RENAME TO categories');
        $this->addSql('ALTER TABLE highlight RENAME TO highlights');
        $this->addSql('ALTER INDEX idx_c998d83412469de2 RENAME TO idx_3a73a36612469de2');
        $this->addSql('ALTER TABLE highlights RENAME CONSTRAINT fk_c998d83412469de2 TO fk_3a73a36612469de2');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE highlights RENAME CONSTRAINT fk_3a73a36612469de2 TO fk_c998d83412469de2');
        $this->addSql('ALTER INDEX idx_3a73a36612469de2 RENAME TO idx_c998d83412469de2');
        $this->addSql('ALTER TABLE highlights RENAME TO highlight');
        $this->addSql('ALTER TABLE categories RENAME TO category');
    }
}
