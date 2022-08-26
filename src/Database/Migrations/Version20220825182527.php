<?php

declare(strict_types=1);

namespace Api\Database\Migrations;

use DateTime;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220825182527 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Table to store users and their credentials';
    }

    public function up(Schema $schema): void
    {
        $users = $schema->createTable('users');

        $users->addColumn('id', Types::INTEGER, ['autoincrement' => true, 'unsigned' => true]);
        $users->addColumn('name', Types::STRING);
        $users->addColumn('email', Types::STRING);
        $users->addColumn('password', Types::STRING);
        $users->addColumn('mfa_secret', Types::STRING, ['notnull' => false]);
        $users->addColumn('tmp_secret', Types::STRING, ['notnull' => false]);
        $users->addColumn('is_admin', Types::BOOLEAN, ['default' => false]);
        $users->addColumn('is_active', Types::BOOLEAN, ['default' => true]);
        $users->addColumn('created_at', Types::DATETIME_MUTABLE);
        $users->addColumn('updated_at', Types::DATETIME_MUTABLE, ['notnull' => false]);

        $users->setPrimaryKey(['id']);

    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('users');

    }

    public function postUp(Schema $schema): void
    {
        $this->connection->insert('users', [
            'name' => 'John Doe',
            'email' => 'john.doe@email.com',
            'password' => password_hash('password', PASSWORD_ARGON2I),
            'is_admin' => true,
            'created_at' => 'now()',
        ]);
    }
}
