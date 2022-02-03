<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220203140149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add event store table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('event_store');
        $table->addColumn('uuid', "string", [
            'length' => 36,
            'fixed' => true,
        ]);
        $table->addColumn('playhead', "integer", [
            'unsigned' => true,
        ]);
        $table->addColumn('payload', "text");
        $table->addColumn('metadata', "text");
        $table->addColumn('recorded_on', "string", [
            'length' => 32,
        ]);
        $table->addColumn('type', "text");
        $table->addUniqueIndex(['uuid', 'playhead']);
        $table->setPrimaryKey(['uuid', 'playhead']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('event_store');
    }
}
