<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20221108174020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add messenger_messages tables.';
    }

    public function up(Schema $schema): void
    {
        $userTable = $schema->createTable('messenger_messages');

        $userTable->addColumn(
            'id',
            Types::BIGINT,
            ['autoincrement' => true]
        );

        $userTable->setPrimaryKey(['id']);

        $userTable->addColumn(
            'body',
            Types::BLOB
        );

        $userTable->addColumn(
            'headers',
            Types::BLOB
        );

        $userTable->addColumn(
            'queue_name',
            Types::STRING,
            ['length' => 190]
        );

        $userTable->addIndex(
            ['queue_name'],
            'IDX_75EA56E0FB7336F0'
        );

        $userTable->addColumn(
            'created_at',
            Types::DATETIME_MUTABLE
        );

        $userTable->addColumn(
            'available_at',
            Types::DATETIME_MUTABLE
        );

        $userTable->addIndex(
            ['available_at'],
            'IDX_75EA56E0E3BD61CE'
        );

        $userTable->addColumn(
            'delivered_at',
            Types::DATETIME_MUTABLE
        );

        $userTable->addIndex(
            ['delivered_at'],
            'IDX_75EA56E016BA31DB'
        );
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('messenger_messages');
    }
}
