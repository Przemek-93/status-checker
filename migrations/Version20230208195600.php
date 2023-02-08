<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230208195600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Increase length of checking_type column to 20 characters';
    }

    public function up(Schema $schema): void
    {
       $schema
           ->getTable('notification')
           ->getColumn('checking_type')
           ->setOptions(['length' => 20]);
    }

    public function down(Schema $schema): void
    {
        $schema
            ->getTable('notification')
            ->getColumn('checking_type')
            ->setOptions(['length' => 10]);
    }
}
