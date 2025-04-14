<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250330140812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis DROP FOREIGN KEY fk_av
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis DROP FOREIGN KEY fk_avis
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chatconversation DROP FOREIGN KEY fk_conver
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chatconversation DROP FOREIGN KEY fk_conv
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chatsession DROP FOREIGN KEY fk_sess
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chatsession DROP FOREIGN KEY fk_session
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande DROP FOREIGN KEY fk_comm
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande DROP FOREIGN KEY fk_commans
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande DROP FOREIGN KEY fk_l
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime DROP FOREIGN KEY fk_avi
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime DROP FOREIGN KEY fk_userr
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie DROP FOREIGN KEY fk_reclamation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie DROP FOREIGN KEY fk_reclamation_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie DROP FOREIGN KEY fk_m
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie DROP FOREIGN KEY fk_user_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre DROP FOREIGN KEY offre_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier DROP FOREIGN KEY fk_p
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier DROP FOREIGN KEY fk_pan
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation DROP FOREIGN KEY fk_rec
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation DROP FOREIGN KEY fk_user_reclamation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY fk_voiture
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY fk_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock DROP FOREIGN KEY fk_stock
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture DROP FOREIGN KEY fk_voiture_categorie
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE avis
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE camion_remorquage
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE categorie
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE chatconversation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE chatsession
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE commande
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE equipement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lignecommande
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE mention_j_aime
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messagerie
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE offre
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE panier
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reclamation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE res_mecanicien
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE res_remorquage
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE res_testdrive
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE service_remorquage
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE stock
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE voiture
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE voiture_remorquage
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE avis (id_a INT AUTO_INCREMENT NOT NULL, id INT NOT NULL, id_v INT NOT NULL, note INT NOT NULL, commentaire TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, dateavis DATETIME NOT NULL, INDEX fk_avis (id), INDEX fk_av (id_v), PRIMARY KEY(id_a)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE camion_remorquage (id_cr INT AUTO_INCREMENT NOT NULL, modele VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, annee INT NOT NULL, num_tel VARCHAR(8) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, statut VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT 'Disponible' NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id_cr)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE categorie (id_c INT AUTO_INCREMENT NOT NULL, type VARCHAR(45) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, type_carburant VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, type_utilisation VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, nbr_porte INT NOT NULL, transmission VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id_c)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE chatconversation (id_conv INT AUTO_INCREMENT NOT NULL, id_sender INT NOT NULL, id_receiver INT NOT NULL, message TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX fk_conv (id_sender), INDEX fk_conver (id_receiver), PRIMARY KEY(id_conv)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE chatsession (id_session INT AUTO_INCREMENT NOT NULL, id_user1 INT NOT NULL, id_user2 INT NOT NULL, INDEX fk_session (id_user2), INDEX fk_sess (id_user1), PRIMARY KEY(id_session)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE commande (id_com INT AUTO_INCREMENT NOT NULL, id INT NOT NULL, date_com DATETIME NOT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, montant_total DOUBLE PRECISION NOT NULL, INDEX fk_comm (id), PRIMARY KEY(id_com)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE equipement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, image TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, reference VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, marque VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lignecommande (id_l INT AUTO_INCREMENT NOT NULL, id_e INT NOT NULL, idc INT NOT NULL, quantite INT NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL, INDEX fk_commans (idc), INDEX fk_l (id_e), PRIMARY KEY(id_l)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE mention_j_aime (id_user INT NOT NULL, id_a INT NOT NULL, id_mention INT NOT NULL, INDEX fk_userr (id_user), INDEX fk_avi (id_a)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messagerie (id_m INT AUTO_INCREMENT NOT NULL, id_rec INT NOT NULL, id_user INT DEFAULT NULL, sender VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, message TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, datemessage DATETIME NOT NULL, receiver VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX fk_m (id_rec), INDEX id_user (id_user), PRIMARY KEY(id_m)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE offre (id_offre INT AUTO_INCREMENT NOT NULL, id_equipement INT DEFAULT NULL, type_offre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, taux_reduction DOUBLE PRECISION NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, INDEX id_equipement (id_equipement), PRIMARY KEY(id_offre)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE panier (id_p INT AUTO_INCREMENT NOT NULL, id INT NOT NULL, id_e INT NOT NULL, quantite INT NOT NULL, INDEX fk_pan (id_e), INDEX fk_p (id), PRIMARY KEY(id_p)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reclamation (id_rec INT AUTO_INCREMENT NOT NULL, id INT NOT NULL, titre VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, contenu TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, datecreation DATETIME NOT NULL, INDEX fk_rec (id), PRIMARY KEY(id_rec)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reservation (id_r INT AUTO_INCREMENT NOT NULL, id_v INT NOT NULL, id INT NOT NULL, date_res DATETIME NOT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX fk_voiture (id_v), INDEX fk_user (id), PRIMARY KEY(id_r)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE res_mecanicien (id_res_m INT NOT NULL, id_u INT DEFAULT NULL, id_mec INT DEFAULT NULL, adresse VARCHAR(25) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, note VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, date DATE DEFAULT NULL, INDEX id_u (id_u), INDEX id_mec (id_mec), PRIMARY KEY(id_res_m)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE res_remorquage (id_rem INT NOT NULL, id_cr INT DEFAULT NULL, id_u INT DEFAULT NULL, point_ramassage VARCHAR(25) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, point_depot VARCHAR(25) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, date DATE NOT NULL, INDEX id_cr (id_cr), INDEX id_u (id_u), PRIMARY KEY(id_rem)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE res_testdrive (id_td INT NOT NULL, id_u INT DEFAULT NULL, id_v INT DEFAULT NULL, date DATE DEFAULT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT 'en_cours_de_traitement' NOT NULL COLLATE `utf8mb4_general_ci`, INDEX id_u (id_u), INDEX id_v (id_v), PRIMARY KEY(id_td)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE service_remorquage (id_service INT NOT NULL, id_vr INT NOT NULL, nom_chauffeur VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, lieu VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, date_service DATETIME NOT NULL, statut VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX id_vr (id_vr), PRIMARY KEY(id_service)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE stock (id_s INT AUTO_INCREMENT NOT NULL, id INT NOT NULL, quantite INT NOT NULL, prixvente DOUBLE PRECISION NOT NULL, INDEX fk_stock (id), UNIQUE INDEX id (id), PRIMARY KEY(id_s)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, cin INT NOT NULL, nom VARCHAR(45) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prenom VARCHAR(45) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, tel INT NOT NULL, email VARCHAR(45) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, password VARCHAR(45) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, role VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, adresse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, username VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, photo_profile VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, ban VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT 'non' COLLATE `utf8mb4_general_ci`, question VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, reponse VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, UNIQUE INDEX unique_username (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE voiture (id_v INT AUTO_INCREMENT NOT NULL, id_c INT NOT NULL, marque VARCHAR(45) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, kilometrage INT NOT NULL, couleur VARCHAR(45) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prix DOUBLE PRECISION NOT NULL, image TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, disponibilite VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT 'oui' NOT NULL COLLATE `utf8mb4_general_ci`, INDEX fk_voiture_categorie (id_c), PRIMARY KEY(id_v)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE voiture_remorquage (id_vr INT NOT NULL, marque VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, modele VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, annee INT NOT NULL, num_agence VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, statut VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id_vr)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD CONSTRAINT fk_av FOREIGN KEY (id_v) REFERENCES voiture (id_v)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD CONSTRAINT fk_avis FOREIGN KEY (id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chatconversation ADD CONSTRAINT fk_conver FOREIGN KEY (id_receiver) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chatconversation ADD CONSTRAINT fk_conv FOREIGN KEY (id_sender) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chatsession ADD CONSTRAINT fk_sess FOREIGN KEY (id_user1) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chatsession ADD CONSTRAINT fk_session FOREIGN KEY (id_user2) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande ADD CONSTRAINT fk_comm FOREIGN KEY (id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande ADD CONSTRAINT fk_commans FOREIGN KEY (idc) REFERENCES commande (id_com)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lignecommande ADD CONSTRAINT fk_l FOREIGN KEY (id_e) REFERENCES equipement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime ADD CONSTRAINT fk_avi FOREIGN KEY (id_a) REFERENCES avis (id_a)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mention_j_aime ADD CONSTRAINT fk_userr FOREIGN KEY (id_user) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie ADD CONSTRAINT fk_reclamation FOREIGN KEY (id_rec) REFERENCES reclamation (id_rec) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie ADD CONSTRAINT fk_reclamation_id FOREIGN KEY (id_rec) REFERENCES reclamation (id_rec) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie ADD CONSTRAINT fk_m FOREIGN KEY (id_rec) REFERENCES reclamation (id_rec) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagerie ADD CONSTRAINT fk_user_id FOREIGN KEY (id_user) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre ADD CONSTRAINT offre_ibfk_1 FOREIGN KEY (id_equipement) REFERENCES equipement (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier ADD CONSTRAINT fk_p FOREIGN KEY (id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE panier ADD CONSTRAINT fk_pan FOREIGN KEY (id_e) REFERENCES equipement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation ADD CONSTRAINT fk_rec FOREIGN KEY (id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation ADD CONSTRAINT fk_user_reclamation FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT fk_voiture FOREIGN KEY (id_v) REFERENCES voiture (id_v)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT fk_user FOREIGN KEY (id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock ADD CONSTRAINT fk_stock FOREIGN KEY (id) REFERENCES equipement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE voiture ADD CONSTRAINT fk_voiture_categorie FOREIGN KEY (id_c) REFERENCES categorie (id_c)
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
