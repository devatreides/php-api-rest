<?php

declare(strict_types=1);

namespace Api\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220826172711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'including unique constraint on users table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('alter table users add constraint users_email_unique unique (email)');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('alter table users drop constraint users_email_unique');
    }
}
