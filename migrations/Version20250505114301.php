<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250505114301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cart DROP FOREIGN KEY FK_BA388B719EB6921
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_BA388B719EB6921 ON cart
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart CHANGE client_id client_profile_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart ADD CONSTRAINT FK_BA388B75CAE2FF9 FOREIGN KEY (client_profile_id) REFERENCES client_profile (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_BA388B75CAE2FF9 ON cart (client_profile_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list DROP FOREIGN KEY FK_5B8739BD19EB6921
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_5B8739BD19EB6921 ON wish_list
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list CHANGE client_id client_profile_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list ADD CONSTRAINT FK_5B8739BD5CAE2FF9 FOREIGN KEY (client_profile_id) REFERENCES client_profile (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_5B8739BD5CAE2FF9 ON wish_list (client_profile_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cart DROP FOREIGN KEY FK_BA388B75CAE2FF9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_BA388B75CAE2FF9 ON cart
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart CHANGE client_profile_id client_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart ADD CONSTRAINT FK_BA388B719EB6921 FOREIGN KEY (client_id) REFERENCES client_profile (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_BA388B719EB6921 ON cart (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list DROP FOREIGN KEY FK_5B8739BD5CAE2FF9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_5B8739BD5CAE2FF9 ON wish_list
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list CHANGE client_profile_id client_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list ADD CONSTRAINT FK_5B8739BD19EB6921 FOREIGN KEY (client_id) REFERENCES client_profile (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_5B8739BD19EB6921 ON wish_list (client_id)
        SQL);
    }
}
