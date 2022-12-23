<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20221108175124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user table.';
    }

    public function up(Schema $schema): void
    {
        $userTable = $schema->createTable('user');

        $userTable->addColumn(
            'id',
            Types::INTEGER,
            ['autoincrement' => true]
        );

        $userTable->setPrimaryKey(['id']);

        $userTable->addColumn(
            'email',
            Types::STRING,
            ['length' => 255]
        );

        $userTable->addUniqueIndex(['email']);

        $userTable->addColumn(
            'name',
            Types::STRING,
            ['length' => 255]
        );

        $userTable->addColumn(
            'roles',
            Types::JSON
        );

        $userTable->addColumn(
            'password',
            Types::STRING,
            ['length' => 255]
        );
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('user');
    }
}
