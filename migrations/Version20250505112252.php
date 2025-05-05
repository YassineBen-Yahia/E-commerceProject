<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250505112252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list DROP FOREIGN KEY FK_5B8739BDFB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_5B8739BDFB88E14F ON wish_list
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list CHANGE utilisateur_id client_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list ADD CONSTRAINT FK_5B8739BD19EB6921 FOREIGN KEY (client_id) REFERENCES client_profile (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_5B8739BD19EB6921 ON wish_list (client_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list DROP FOREIGN KEY FK_5B8739BD19EB6921
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_5B8739BD19EB6921 ON wish_list
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list CHANGE client_id utilisateur_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list ADD CONSTRAINT FK_5B8739BDFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES client_profile (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_5B8739BDFB88E14F ON wish_list (utilisateur_id)
        SQL);
    }
}
