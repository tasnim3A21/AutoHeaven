<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250502073326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON avis
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis DROP id_a, CHANGE commentaire commentaire LONGTEXT NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0BF396750 FOREIGN KEY (id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0ACF191FB FOREIGN KEY (id_v) REFERENCES voiture (id_v)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_voiture2 ON avis
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8F91ABF0ACF191FB ON avis (id_v)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande DROP FOREIGN KEY fk_comm
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande CHANGE id id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_comm ON commande
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6EEAA67DBF396750 ON commande (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande ADD CONSTRAINT fk_comm FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipement CHANGE description description LONGTEXT NOT NULL, CHANGE image image LONGTEXT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande DROP FOREIGN KEY fk_l
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande DROP FOREIGN KEY fk_commans
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande DROP FOREIGN KEY fk_l
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande CHANGE id_e id_e INT DEFAULT NULL, CHANGE idc idc INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande ADD CONSTRAINT FK_853B7939284FD025 FOREIGN KEY (id_e) REFERENCES equipement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_l ON lignecommande
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_853B7939284FD025 ON lignecommande (id_e)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_commans ON lignecommande
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_853B79396D6DB7FC ON lignecommande (idc)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande ADD CONSTRAINT fk_commans FOREIGN KEY (idc) REFERENCES commande (id_com) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande ADD CONSTRAINT fk_l FOREIGN KEY (id_e) REFERENCES equipement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime DROP FOREIGN KEY fk_userr
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime DROP FOREIGN KEY fk_avi
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime DROP FOREIGN KEY fk_userr
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime DROP FOREIGN KEY fk_avi
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime CHANGE id_mention id_mention INT NOT NULL, CHANGE id_user id_user INT DEFAULT NULL, CHANGE id_a id_a INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime ADD CONSTRAINT FK_F237AF556B3CA4B FOREIGN KEY (id_user) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime ADD CONSTRAINT FK_F237AF552F22143C FOREIGN KEY (id_a) REFERENCES avis (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_userr ON mention_j_aime
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F237AF556B3CA4B ON mention_j_aime (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_avi ON mention_j_aime
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F237AF552F22143C ON mention_j_aime (id_a)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime ADD CONSTRAINT fk_userr FOREIGN KEY (id_user) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime ADD CONSTRAINT fk_avi FOREIGN KEY (id_a) REFERENCES avis (id_a)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie DROP FOREIGN KEY fk_m
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie DROP FOREIGN KEY fk_user_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie DROP FOREIGN KEY fk_reclamation
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX id_user ON messagerie
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie DROP FOREIGN KEY fk_reclamation_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie DROP FOREIGN KEY fk_m
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie DROP FOREIGN KEY fk_reclamation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie CHANGE id_rec id_rec INT DEFAULT NULL, CHANGE message message LONGTEXT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_m ON messagerie
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_14E8F60CFAA12276 ON messagerie (id_rec)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie ADD CONSTRAINT fk_reclamation_id FOREIGN KEY (id_rec) REFERENCES reclamation (id_rec) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie ADD CONSTRAINT fk_m FOREIGN KEY (id_rec) REFERENCES reclamation (id_rec) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie ADD CONSTRAINT fk_reclamation FOREIGN KEY (id_rec) REFERENCES reclamation (id_rec) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA806F0F5C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification CHANGE equipement_id equipement_id INT NOT NULL, CHANGE message message LONGTEXT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_bf5476ca806f0f5c ON notification
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF5476CA806F0F5C ON notification (equipement_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre DROP FOREIGN KEY offre_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre DROP FOREIGN KEY offre_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre CHANGE type_offre type_offre VARCHAR(255) NOT NULL, CHANGE description description VARCHAR(255) NOT NULL, CHANGE image image VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre ADD CONSTRAINT FK_AF86866F1D3E4624 FOREIGN KEY (id_equipement) REFERENCES equipement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX id_equipement ON offre
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AF86866F1D3E4624 ON offre (id_equipement)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre ADD CONSTRAINT offre_ibfk_1 FOREIGN KEY (id_equipement) REFERENCES equipement (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier DROP FOREIGN KEY fk_p
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier DROP FOREIGN KEY fk_pan
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier DROP FOREIGN KEY fk_p
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier DROP FOREIGN KEY fk_pan
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier CHANGE id id INT DEFAULT NULL, CHANGE id_e id_e INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2284FD025 FOREIGN KEY (id_e) REFERENCES equipement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_p ON panier
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_24CC0DF2BF396750 ON panier (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_pan ON panier
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_24CC0DF2284FD025 ON panier (id_e)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier ADD CONSTRAINT fk_p FOREIGN KEY (id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier ADD CONSTRAINT fk_pan FOREIGN KEY (id_e) REFERENCES equipement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation DROP FOREIGN KEY fk_rec
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation DROP FOREIGN KEY fk_user_reclamation
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_rec ON reclamation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_mecanicien CHANGE adresse adresse VARCHAR(25) DEFAULT NULL, CHANGE note note VARCHAR(100) NOT NULL, CHANGE date date DATE DEFAULT NULL, CHANGE status status VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_mecanicien ADD CONSTRAINT FK_4A75B64F35F8C041 FOREIGN KEY (id_u) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_mecanicien ADD CONSTRAINT FK_4A75B64FEDDBC63B FOREIGN KEY (id_mec) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX id_u ON res_mecanicien
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4A75B64F35F8C041 ON res_mecanicien (id_u)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX id_mec ON res_mecanicien
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4A75B64FEDDBC63B ON res_mecanicien (id_mec)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_remorquage CHANGE point_ramassage point_ramassage VARCHAR(255) DEFAULT NULL, CHANGE point_depot point_depot VARCHAR(255) DEFAULT NULL, CHANGE date date DATE DEFAULT NULL, CHANGE status status VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_remorquage ADD CONSTRAINT FK_2364F917717FC38C FOREIGN KEY (id_cr) REFERENCES camion_remorquage (id_cr)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_remorquage ADD CONSTRAINT FK_2364F91735F8C041 FOREIGN KEY (id_u) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX id_cr ON res_remorquage
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2364F917717FC38C ON res_remorquage (id_cr)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX id_u ON res_remorquage
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2364F91735F8C041 ON res_remorquage (id_u)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_testdrive CHANGE id_u id_u INT NOT NULL, CHANGE id_v id_v INT NOT NULL, CHANGE status status VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_testdrive ADD CONSTRAINT FK_9CBAC69735F8C041 FOREIGN KEY (id_u) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_testdrive ADD CONSTRAINT FK_9CBAC697ACF191FB FOREIGN KEY (id_v) REFERENCES voiture (id_v)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX id_u ON res_testdrive
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9CBAC69735F8C041 ON res_testdrive (id_u)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX id_v ON res_testdrive
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9CBAC697ACF191FB ON res_testdrive (id_v)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY fk_voiture
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY fk_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY fk_voiture
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY fk_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation CHANGE id_r id_r INT NOT NULL, CHANGE id_v id_v INT DEFAULT NULL, CHANGE id id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955ACF191FB FOREIGN KEY (id_v) REFERENCES voiture (id_v) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_voiture ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C84955ACF191FB ON reservation (id_v)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_user ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C84955BF396750 ON reservation (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT fk_voiture FOREIGN KEY (id_v) REFERENCES voiture (id_v)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT fk_user FOREIGN KEY (id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX id_vr ON service_remorquage
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock DROP FOREIGN KEY fk_stock
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_stock ON stock
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock DROP FOREIGN KEY fk_stock
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock CHANGE id id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock ADD CONSTRAINT FK_4B365660BF396750 FOREIGN KEY (id) REFERENCES equipement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX id ON stock
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_4B365660BF396750 ON stock (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock ADD CONSTRAINT fk_stock FOREIGN KEY (id) REFERENCES equipement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE role role VARCHAR(255) DEFAULT NULL, CHANGE ban ban VARCHAR(10) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649ABE530DA ON user (cin)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649F037AB0F ON user (tel)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX unique_username ON user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture DROP FOREIGN KEY fk_voiture_categorie
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture DROP FOREIGN KEY fk_voiture_categorie
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture CHANGE id_c id_c INT DEFAULT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE image image LONGTEXT NOT NULL, CHANGE disponibilite disponibilite VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810FC12C7510 FOREIGN KEY (id_c) REFERENCES categorie (id_c) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_voiture_categorie ON voiture
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E9E2810FC12C7510 ON voiture (id_c)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture ADD CONSTRAINT fk_voiture_categorie FOREIGN KEY (id_c) REFERENCES categorie (id_c)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE avis MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0ACF191FB
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON avis
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0ACF191FB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD id_a INT NOT NULL, CHANGE id id INT NOT NULL, CHANGE commentaire commentaire TEXT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD PRIMARY KEY (id_a)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_8f91abf0acf191fb ON avis
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_voiture2 ON avis (id_v)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0ACF191FB FOREIGN KEY (id_v) REFERENCES voiture (id_v)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DBF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_6eeaa67dbf396750 ON commande
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_comm ON commande (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DBF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipement CHANGE description description TEXT NOT NULL, CHANGE image image TEXT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande DROP FOREIGN KEY FK_853B7939284FD025
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande DROP FOREIGN KEY FK_853B7939284FD025
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande DROP FOREIGN KEY FK_853B79396D6DB7FC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande CHANGE id_e id_e INT NOT NULL, CHANGE idc idc INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande ADD CONSTRAINT fk_l FOREIGN KEY (id_e) REFERENCES equipement (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_853b7939284fd025 ON lignecommande
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_l ON lignecommande (id_e)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_853b79396d6db7fc ON lignecommande
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_commans ON lignecommande (idc)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande ADD CONSTRAINT FK_853B7939284FD025 FOREIGN KEY (id_e) REFERENCES equipement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande ADD CONSTRAINT FK_853B79396D6DB7FC FOREIGN KEY (idc) REFERENCES commande (id_com) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime DROP FOREIGN KEY FK_F237AF556B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime DROP FOREIGN KEY FK_F237AF552F22143C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime DROP FOREIGN KEY FK_F237AF556B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime DROP FOREIGN KEY FK_F237AF552F22143C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime CHANGE id_mention id_mention INT AUTO_INCREMENT NOT NULL, CHANGE id_user id_user INT NOT NULL, CHANGE id_a id_a INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime ADD CONSTRAINT fk_userr FOREIGN KEY (id_user) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime ADD CONSTRAINT fk_avi FOREIGN KEY (id_a) REFERENCES avis (id_a)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_f237af556b3ca4b ON mention_j_aime
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_userr ON mention_j_aime (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_f237af552f22143c ON mention_j_aime
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_avi ON mention_j_aime (id_a)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime ADD CONSTRAINT FK_F237AF556B3CA4B FOREIGN KEY (id_user) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime ADD CONSTRAINT FK_F237AF552F22143C FOREIGN KEY (id_a) REFERENCES avis (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie DROP FOREIGN KEY FK_14E8F60CFAA12276
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie CHANGE id_rec id_rec INT NOT NULL, CHANGE message message TEXT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie ADD CONSTRAINT fk_user_id FOREIGN KEY (id_user) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX id_user ON messagerie (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_14e8f60cfaa12276 ON messagerie
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_m ON messagerie (id_rec)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie ADD CONSTRAINT FK_14E8F60CFAA12276 FOREIGN KEY (id_rec) REFERENCES reclamation (id_rec) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA806F0F5C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification CHANGE equipement_id equipement_id INT DEFAULT NULL, CHANGE message message VARCHAR(255) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_bf5476ca806f0f5c ON notification
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX FK_BF5476CA806F0F5C ON notification (equipement_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F1D3E4624
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F1D3E4624
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre CHANGE type_offre type_offre VARCHAR(255) DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre ADD CONSTRAINT offre_ibfk_1 FOREIGN KEY (id_equipement) REFERENCES equipement (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_af86866f1d3e4624 ON offre
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX id_equipement ON offre (id_equipement)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre ADD CONSTRAINT FK_AF86866F1D3E4624 FOREIGN KEY (id_equipement) REFERENCES equipement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2284FD025
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2284FD025
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier CHANGE id id INT NOT NULL, CHANGE id_e id_e INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier ADD CONSTRAINT fk_p FOREIGN KEY (id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier ADD CONSTRAINT fk_pan FOREIGN KEY (id_e) REFERENCES equipement (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_24cc0df2284fd025 ON panier
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_pan ON panier (id_e)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_24cc0df2bf396750 ON panier
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_p ON panier (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2284FD025 FOREIGN KEY (id_e) REFERENCES equipement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation ADD CONSTRAINT fk_rec FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation ADD CONSTRAINT fk_user_reclamation FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_rec ON reclamation (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955ACF191FB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955ACF191FB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation CHANGE id_r id_r INT AUTO_INCREMENT NOT NULL, CHANGE id_v id_v INT NOT NULL, CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT fk_voiture FOREIGN KEY (id_v) REFERENCES voiture (id_v)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT fk_user FOREIGN KEY (id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_42c84955acf191fb ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_voiture ON reservation (id_v)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_42c84955bf396750 ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_user ON reservation (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955ACF191FB FOREIGN KEY (id_v) REFERENCES voiture (id_v) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_mecanicien DROP FOREIGN KEY FK_4A75B64F35F8C041
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_mecanicien DROP FOREIGN KEY FK_4A75B64FEDDBC63B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_mecanicien DROP FOREIGN KEY FK_4A75B64F35F8C041
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_mecanicien DROP FOREIGN KEY FK_4A75B64FEDDBC63B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_mecanicien CHANGE adresse adresse VARCHAR(25) NOT NULL, CHANGE note note VARCHAR(100) DEFAULT NULL, CHANGE date date DATE NOT NULL, CHANGE status status VARCHAR(255) DEFAULT 'en_cours_de_traitement' NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_4a75b64f35f8c041 ON res_mecanicien
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX id_u ON res_mecanicien (id_u)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_4a75b64feddbc63b ON res_mecanicien
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX id_mec ON res_mecanicien (id_mec)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_mecanicien ADD CONSTRAINT FK_4A75B64F35F8C041 FOREIGN KEY (id_u) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_mecanicien ADD CONSTRAINT FK_4A75B64FEDDBC63B FOREIGN KEY (id_mec) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_remorquage DROP FOREIGN KEY FK_2364F917717FC38C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_remorquage DROP FOREIGN KEY FK_2364F91735F8C041
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_remorquage DROP FOREIGN KEY FK_2364F917717FC38C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_remorquage DROP FOREIGN KEY FK_2364F91735F8C041
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_remorquage CHANGE point_ramassage point_ramassage VARCHAR(255) NOT NULL, CHANGE point_depot point_depot VARCHAR(255) NOT NULL, CHANGE date date DATE NOT NULL, CHANGE status status VARCHAR(255) DEFAULT 'en_cours_de_traitement' NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_2364f917717fc38c ON res_remorquage
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX id_cr ON res_remorquage (id_cr)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_2364f91735f8c041 ON res_remorquage
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX id_u ON res_remorquage (id_u)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_remorquage ADD CONSTRAINT FK_2364F917717FC38C FOREIGN KEY (id_cr) REFERENCES camion_remorquage (id_cr)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_remorquage ADD CONSTRAINT FK_2364F91735F8C041 FOREIGN KEY (id_u) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_testdrive DROP FOREIGN KEY FK_9CBAC69735F8C041
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_testdrive DROP FOREIGN KEY FK_9CBAC697ACF191FB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_testdrive DROP FOREIGN KEY FK_9CBAC69735F8C041
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_testdrive DROP FOREIGN KEY FK_9CBAC697ACF191FB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_testdrive CHANGE id_u id_u INT DEFAULT NULL, CHANGE id_v id_v INT DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT 'en_cours_de_traitement' NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_9cbac69735f8c041 ON res_testdrive
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX id_u ON res_testdrive (id_u)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_9cbac697acf191fb ON res_testdrive
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX id_v ON res_testdrive (id_v)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_testdrive ADD CONSTRAINT FK_9CBAC69735F8C041 FOREIGN KEY (id_u) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE res_testdrive ADD CONSTRAINT FK_9CBAC697ACF191FB FOREIGN KEY (id_v) REFERENCES voiture (id_v)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX id_vr ON service_remorquage (id_vr)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock DROP FOREIGN KEY FK_4B365660BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock DROP FOREIGN KEY FK_4B365660BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock ADD CONSTRAINT fk_stock FOREIGN KEY (id) REFERENCES equipement (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_stock ON stock (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_4b365660bf396750 ON stock
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX id ON stock (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock ADD CONSTRAINT FK_4B365660BF396750 FOREIGN KEY (id) REFERENCES equipement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8D93D649ABE530DA ON user
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8D93D649F037AB0F ON user
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8D93D649E7927C74 ON user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE role role VARCHAR(255) NOT NULL, CHANGE ban ban VARCHAR(255) DEFAULT 'non'
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_8d93d649f85e0677 ON user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX unique_username ON user (username)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810FC12C7510
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810FC12C7510
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture CHANGE id_c id_c INT NOT NULL, CHANGE description description TEXT NOT NULL, CHANGE image image TEXT NOT NULL, CHANGE disponibilite disponibilite VARCHAR(255) DEFAULT 'oui' NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture ADD CONSTRAINT fk_voiture_categorie FOREIGN KEY (id_c) REFERENCES categorie (id_c)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_e9e2810fc12c7510 ON voiture
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_voiture_categorie ON voiture (id_c)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810FC12C7510 FOREIGN KEY (id_c) REFERENCES categorie (id_c) ON DELETE CASCADE
        SQL);
    }
}
