<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220317065743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add status to read_model_mars_rover table';
    }

    public function up(Schema $schema): void
    {
        $sql = <<<SQL
            UPDATE `read_model_mars_rover`
            SET status='created' WHERE coordinate_x IS NULL            
        SQL;
        $this->addSql($sql);

        $sql = <<<SQL
            UPDATE `read_model_mars_rover`
            SET status='placed' WHERE coordinate_x IS NOT NULL            
        SQL;
        $this->addSql($sql);
    }

    public function down(Schema $schema): void
    {
    }
}
