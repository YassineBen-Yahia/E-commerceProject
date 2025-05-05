<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250505155912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_item_produit DROP FOREIGN KEY FK_5477581AE9B59A59
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_item_produit DROP FOREIGN KEY FK_5477581AF347EFB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cart_item_produit
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_item ADD produit_id INT DEFAULT NULL, ADD commande_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE2527F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE252782EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F0FE2527F347EFB ON cart_item (produit_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F0FE252782EA2E54 ON cart_item (commande_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE cart_item_produit (cart_item_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_5477581AE9B59A59 (cart_item_id), INDEX IDX_5477581AF347EFB (produit_id), PRIMARY KEY(cart_item_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_item_produit ADD CONSTRAINT FK_5477581AE9B59A59 FOREIGN KEY (cart_item_id) REFERENCES cart_item (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_item_produit ADD CONSTRAINT FK_5477581AF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE2527F347EFB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE252782EA2E54
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F0FE2527F347EFB ON cart_item
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F0FE252782EA2E54 ON cart_item
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_item DROP produit_id, DROP commande_id
        SQL);
    }
}
