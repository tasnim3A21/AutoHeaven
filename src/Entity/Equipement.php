<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Panier;
use App\Entity\Notification;



use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
class Equipement
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]


    #[ORM\GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: "Nom est obligatoire.")] 
    private string $nom;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "Description est obligatoire.")]
    #[Assert\Length(min: 10, minMessage: "La description doit contenir au moins {{ limit }} caractères.")]
    #[Assert\Length(max: 1000, maxMessage: "La description ne doit pas dépasser {{ limit }} caractères.")]
    private string $description;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "L'image est obligatoire.")]
    private string $image;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: "La référence est obligatoire.")]
    private string $reference;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: "La marque est obligatoire.")]
    private string $marque;

    #[ORM\OneToMany(mappedBy: "id_equipement", targetEntity: Offre::class)]
    private Collection $offres;

    #[ORM\OneToOne(mappedBy: "equipement", targetEntity: Stock::class, cascade: ["persist", "remove"])]
    private ?Stock $stock = null;

    #[ORM\OneToMany(mappedBy: "id_e", targetEntity: Lignecommande::class)]
    private Collection $lignecommandes;

    #[ORM\OneToMany(mappedBy: "id_e", targetEntity: Panier::class)]
    private Collection $paniers;
    #[ORM\OneToMany(mappedBy: "equipement", targetEntity: Notification::class, cascade: ["remove"])]
    private Collection $notifications;
    public function getNotifications(): Collection
{
    return $this->notifications;
}

public function addNotification(Notification $notification): self
{
    if (!$this->notifications->contains($notification)) {
        $this->notifications[] = $notification;
        $notification->setEquipement($this);
    }

    return $this;
}

public function removeNotification(Notification $notification): self
{
    if ($this->notifications->removeElement($notification)) {
        if ($notification->getEquipement() === $this) {
            $notification->setEquipement(null);
        }
    }

    return $this;
}



    public function getOffres(): Collection
    {
        return $this->offres;
    }

    public function addOffre(Offre $offre): self
    {
        if (!$this->offres->contains($offre)) {
            $this->offres[] = $offre;
            $offre->setIdEquipement($this);
        }
        return $this;
    }

    public function removeOffre(Offre $offre): self
    {
        if ($this->offres->removeElement($offre)) {
            if ($offre->getIdEquipement() === $this) {
                $offre->setIdEquipement(null);
            }
        }
        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock): self
    {
        if ($stock === null && $this->stock !== null) {
            $this->stock->setEquipement(null);
        }

        if ($stock !== null && $stock->getEquipement() !== $this) {
            $stock->setEquipement($this);
        }

        $this->stock = $stock;
        return $this;
    }

    public function getLignecommandes(): Collection
    {
        return $this->lignecommandes;
    }

    public function addLignecommande(Lignecommande $lignecommande): self
    {
        if (!$this->lignecommandes->contains($lignecommande)) {
            $this->lignecommandes[] = $lignecommande;
            $lignecommande->setIdE($this);
        }
        return $this;
    }

    public function removeLignecommande(Lignecommande $lignecommande): self
    {
        if ($this->lignecommandes->removeElement($lignecommande)) {
            if ($lignecommande->getIdE() === $this) {
                $lignecommande->setIdE(null);
            }
        }
        return $this;
    }

    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers[] = $panier;
            $panier->setIdE($this);
        }
        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        if ($this->paniers->removeElement($panier)) {
            if ($panier->getIdE() === $this) {
                $panier->setIdE(null);
            }
        }
        return $this;
    }

    // Getters et Setters de base
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function getMarque(): string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): self
    {
        $this->marque = $marque;
        return $this;
    }
}


