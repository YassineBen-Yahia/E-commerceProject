<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250505161937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE commande_cart_item DROP FOREIGN KEY FK_E0F27F6582EA2E54
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande_cart_item DROP FOREIGN KEY FK_E0F27F65E9B59A59
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE commande_cart_item
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE commande_cart_item (commande_id INT NOT NULL, cart_item_id INT NOT NULL, INDEX IDX_E0F27F6582EA2E54 (commande_id), INDEX IDX_E0F27F65E9B59A59 (cart_item_id), PRIMARY KEY(commande_id, cart_item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande_cart_item ADD CONSTRAINT FK_E0F27F6582EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande_cart_item ADD CONSTRAINT FK_E0F27F65E9B59A59 FOREIGN KEY (cart_item_id) REFERENCES cart_item (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
    }
}
