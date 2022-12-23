<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20221109164300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add notification and related notification_reading, notification_receiver tables.';
    }

    public function up(Schema $schema): void
    {
        $notificationTable = $schema->createTable('notification');

        $notificationTable->addColumn(
            'id',
            Types::INTEGER,
            ['autoincrement' => true]
        );

        $notificationTable->setPrimaryKey(['id']);

        $notificationTable->addColumn(
            'url',
            Types::STRING,
            ['length' => 255]
        );

        $notificationTable->addColumn(
            'type',
            Types::STRING,
            ['length' => 10]
        );

        $notificationTable->addColumn(
            'http_method',
            Types::STRING,
            ['length' => 10]
        );

        $notificationTable->addColumn(
            'sending_frequency',
            Types::INTEGER
        );

        $notificationTable->addColumn(
            'is_active',
            Types::BOOLEAN,
            ['default' => false]
        );

        $notificationTable->addColumn(
            'sending_date',
            Types::DATETIME_MUTABLE
        );

        $notificationTable->addColumn(
            'sent_at',
            Types::DATETIME_MUTABLE,
            ['notnull' => false]
        );

        $notificationTable->addColumn(
            'created_at',
            Types::DATETIME_MUTABLE,
            ['default' => 'CURRENT_TIMESTAMP']
        );

        $notificationTable->addColumn(
            'updated_at',
            Types::DATETIME_MUTABLE,
            ['default' => 'CURRENT_TIMESTAMP']
        );

        $receiverTable = $schema->createTable('notification_receiver');

        $receiverTable->addColumn(
            'id',
            Types::INTEGER,
            ['autoincrement' => true]
        );

        $receiverTable->setPrimaryKey(['id']);

        $receiverTable->addColumn(
            'notification_id',
            Types::INTEGER
        );

        $receiverTable->addIndex(
            ['notification_id'],
            'IDX_68A8B433EF1A9D84'
        );

        $receiverTable->addColumn(
            'email',
            Types::STRING,
            ['length' => 50]
        );

        $receiverTable->addUniqueIndex(
            ['notification_id', 'email'],
            'UNIQUE_IDX_68A8B433EF1A2D12'
        );

        $receiverTable->addForeignKeyConstraint(
            $notificationTable,
            ['notification_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'FK_68A8B433EF1A9D84'
        );

        $readingTable = $schema->createTable('notification_reading');

        $readingTable->addColumn(
            'id',
            Types::INTEGER,
            ['autoincrement' => true]
        );

        $readingTable->setPrimaryKey(['id']);

        $readingTable->addColumn(
            'notification_id',
            Types::INTEGER
        );

        $readingTable->addIndex(
            ['notification_id'],
            'IDX_D40476F6EF1A9D84'
        );

        $readingTable->addColumn(
            'status',
            Types::INTEGER
        );

        $readingTable->addColumn(
            'content',
            Types::JSON
        );

        $readingTable->addColumn(
            'read_at',
            Types::DATETIME_MUTABLE
        );

        $readingTable->addForeignKeyConstraint(
            $notificationTable,
            ['notification_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'FK_D40476F6EF1A9D84'
        );
    }

    public function down(Schema $schema): void
    {
        $readingTable = $schema->getTable('notification_reading');
        $readingTable->removeForeignKey('FK_D40476F6EF1A9D84');

        $receiverTable = $schema->getTable('notification_receiver');
        $receiverTable->removeForeignKey('FK_68A8B433EF1A9D84');

        $schema->dropTable('notification');
        $schema->dropTable('notification_reading');
        $schema->dropTable('notification_receiver');
    }
}
