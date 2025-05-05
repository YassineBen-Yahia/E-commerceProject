<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250505111709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7FB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart CHANGE utilisateur_id utilisateur_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart ADD CONSTRAINT FK_BA388B7FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES client_profile (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list DROP FOREIGN KEY FK_5B8739BDFB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list ADD CONSTRAINT FK_5B8739BDFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES client_profile (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7FB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart CHANGE utilisateur_id utilisateur_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart ADD CONSTRAINT FK_BA388B7FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list DROP FOREIGN KEY FK_5B8739BDFB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list ADD CONSTRAINT FK_5B8739BDFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
    }
}
