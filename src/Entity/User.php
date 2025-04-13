<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;


use App\Entity\Reservation;



#[ORM\Entity]
class User
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $cin;

    #[ORM\Column(type: "string", length: 45)]
    private string $nom;

    #[ORM\Column(type: "string", length: 45)]
    private string $prenom;

    #[ORM\Column(type: "integer")]
    private int $tel;

    #[ORM\Column(type: "string", length: 45)]
    private string $email;

    #[ORM\Column(type: "string", length: 45)]
    private string $password;

    #[ORM\Column(type: "string")]
    private string $role;

    #[ORM\Column(type: "string", length: 255)]
    private string $adresse;

    #[ORM\Column(type: "string", length: 255)]
    private string $username;

    #[ORM\Column(type: "string", length: 255)]
    private string $photo_profile;

    #[ORM\Column(type: "string", length: 10)]
    private string $ban;

    #[ORM\Column(type: "string", length: 255)]
    private string $question;

    #[ORM\Column(type: "string", length: 255)]
    private string $reponse;

    public function getId()
    {
        return $this->id;
    }



    public function setId($value)
    {
        $this->id = $value;
    }

    public function getCin()
    {
        return $this->cin;
    }

    public function setCin($value)
    {
        $this->cin = $value;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setPrenom($value)
    {
        $this->prenom = $value;
    }

    public function getTel()
    {
        return $this->tel;
    }

    public function setTel($value)
    {
        $this->tel = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($value)
    {
        $this->password = $value;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($value)
    {
        $this->role = $value;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function setAdresse($value)
    {
        $this->adresse = $value;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($value)
    {
        $this->username = $value;
    }

    public function getPhoto_profile()
    {
        return $this->photo_profile;
    }

    public function setPhoto_profile($value)
    {
        $this->photo_profile = $value;
    }

    public function getBan()
    {
        return $this->ban;
    }

    public function setBan($value)
    {
        $this->ban = $value;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function setQuestion($value)
    {
        $this->question = $value;
    }

    public function getReponse()
    {
        return $this->reponse;
    }

    public function setReponse($value)
    {
        $this->reponse = $value;
    }



    #[ORM\OneToMany(mappedBy: "id", targetEntity: Commande::class)]
    private Collection $commandes;

        public function getCommandes(): Collection
        {
            return $this->commandes;
        }
    
        public function addCommande(Commande $commande): self
        {
            if (!$this->commandes->contains($commande)) {
                $this->commandes[] = $commande;
                $commande->setId($this);
            }
    
            return $this;
        }
    
        public function removeCommande(Commande $commande): self
        {
            if ($this->commandes->removeElement($commande)) {
                // set the owning side to null (unless already changed)
                if ($commande->getId() === $this) {
                    $commande->setId(null);
                }
            }
    
            return $this;
        }



    #[ORM\OneToMany(mappedBy: "id", targetEntity: Reclamation::class)]
    private Collection $reclamations;

    #[ORM\OneToMany(mappedBy: "id", targetEntity: Avis::class)]
    private Collection $aviss;



    #[ORM\OneToMany(mappedBy: "id", targetEntity: Commande::class)]
    private Collection $commandes;



    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Mention_j_aime::class)]
    private Collection $mention_j_aimes;

        public function getMention_j_aimes(): Collection
        {
            return $this->mention_j_aimes;
        }
    
        public function addMention_j_aime(Mention_j_aime $mention_j_aime): self
        {
            if (!$this->mention_j_aimes->contains($mention_j_aime)) {
                $this->mention_j_aimes[] = $mention_j_aime;
                $mention_j_aime->setId_user($this);
            }
    
            return $this;
        }
    
        public function removeMention_j_aime(Mention_j_aime $mention_j_aime): self
        {
            if ($this->mention_j_aimes->removeElement($mention_j_aime)) {
                // set the owning side to null (unless already changed)
                if ($mention_j_aime->getId_user() === $this) {
                    $mention_j_aime->setId_user(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Messagerie::class)]
    private Collection $messageries;

    #[ORM\OneToMany(mappedBy: "id", targetEntity: Panier::class)]
    private Collection $paniers;

    #[ORM\OneToMany(mappedBy: "id", targetEntity: Reservation::class)]
    private Collection $reservations;
}
