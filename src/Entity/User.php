<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity]
#[UniqueEntity(
    fields: ['username'],
    message: 'Cet username existe déjà.'
)]
#[UniqueEntity(
    fields: ['email'],
    message: 'Cet email existe déjà.'
)]
#[UniqueEntity(
    fields: ['cin'],
    message: 'Cet CIN existe déjà.'
)]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: "AUTO")]
        #[ORM\Column(type: "integer")]
        private ?int $id = null; // Make sure the ID is nullable initially.
    
        #[ORM\Column(type: "integer", unique: true)]
        #[Assert\NotBlank(message: "Le CIN est obligatoire.")]
        #[Assert\Length(
            exactMessage: "Le CIN doit contenir exactement 8 chiffres.",
            min: 8,
            max: 8
        )]
        #[Assert\Regex(
            pattern: "/^\d{8}$/",
            message: "Le CIN doit contenir uniquement des chiffres."
        )]
        private int $cin;
    
        #[ORM\Column(type: "string", length: 45)]
        #[Assert\NotBlank(message: "Le nom est obligatoire.")]
        private string $nom;
    
        #[ORM\Column(type: "string", length: 45)]
        #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
        private string $prenom;
    
        #[ORM\Column(type: "integer")]
        #[Assert\NotBlank(message: "Le téléphone est obligatoire.")]
        #[Assert\Length(
            exactMessage: "Le téléphone doit contenir exactement 8 chiffres.",
            min: 8,
            max: 8
        )]
        #[Assert\Regex(
            pattern: "/^\d{8}$/",
            message: "Le téléphone doit contenir uniquement des chiffres."
        )]
        private int $tel;
    
        #[ORM\Column(type: "string", length: 45, unique: true)]
        #[Assert\NotBlank(message: "L'email est obligatoire.")]
        #[Assert\Email(message: "L'email doit être valide.")]
        private string $email;
    
        #[ORM\Column(type: "string", length: 255)]
        #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
        private string $password;
    
        #[ORM\Column(type: "string", nullable: true)]
        private ?string $role = null;

        #[ORM\Column(type: "string", length: 255)]
#[Assert\NotBlank(message: "L'adresse est obligatoire.")]
#[Assert\Regex(
    pattern: "/^\d+\sRue\s.*$/",
    message: "L'adresse doit contenir un numéro suivi d'un nom de rue valide."
)]
private string $adresse;
    
        #[ORM\Column(type: "string", length: 255, unique: true)]
        #[Assert\NotBlank(message: "Le nom d'utilisateur est obligatoire.")]
        private string $username;
    
        #[ORM\Column(type: "string", length: 255)]
        private ?string $photo_profile = null;
    
        #[ORM\Column(type: "string", length: 10, nullable: true)]
private ?string $ban = null;

    
        #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $question = null;

#[ORM\Column(type: "string", length: 255, nullable: true)]
private ?string $reponse = null;



    
        // === Symfony Security required methods ===
    
        public function getUserIdentifier(): string
        {
            return $this->email; // or you can use username if that's the identifier
        }
    
        public function getRoles(): array
{
    // Map your custom role to Symfony role format
    return ['ROLE_' . strtoupper($this->role)];
    dump($roleFormatted); // 👈 This will show in Symfony debug toolbar or terminal
    return [$roleFormatted];
}

        
    
        public function eraseCredentials()
        {
            // Clear any sensitive data if stored temporarily
        }
    
        // Already implemented
        public function getPassword(): string
        {
            return $this->password;
        }
    
        // === Your existing getters/setters ===
    
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
            if ($commande->getId() === $this) {
                $commande->setId(null);
            }
        }

        return $this;
    }



    #[ORM\OneToMany(mappedBy: "id", targetEntity: Avis::class)]
    private Collection $aviss;



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


    #[ORM\OneToMany(mappedBy: "id", targetEntity: Reclamation::class)]
    private Collection $reclamations;

}
