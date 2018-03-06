<?php

/*
 * This file is part of MarkdownEditor.
 *
 * (c) Antal Áron <antalaron@antalaron.hu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * @author Antal Áron <antalaron@antalaron.hu>
 */
class Version20180306132611 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE image (id INTEGER NOT NULL, name VARCHAR(80) NOT NULL, type VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, size INTEGER NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE image');
    }
}
