<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Categorie;
use App\Entity\Avis;
use App\Entity\Reservation;

#[ORM\Entity]
class Voiture
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_v;
    #[ORM\OneToMany(mappedBy: "id_v", targetEntity: Avis::class, cascade: ["persist", "remove"])]
    private Collection $avis;
    
    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: "voitures")]
    #[ORM\JoinColumn(name: 'id_c', referencedColumnName: 'id_c', onDelete: 'CASCADE')]
    private Categorie $id_c;

    #[ORM\Column(type: "string", length: 45)]
    private string $marque;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "integer")]
    private int $kilometrage;

    #[ORM\Column(type: "string", length: 45)]
    private string $couleur;

    #[ORM\Column(type: "float")]
    private float $prix;

    #[ORM\Column(type: "text")]
    private string $image;

    #[ORM\Column(type: "string")]
    private string $disponibilite;

    #[ORM\OneToMany(mappedBy: "id_v", targetEntity: Avis::class, cascade: ["persist", "remove"])]
    private Collection $aviss;

    #[ORM\OneToMany(mappedBy: "id_v", targetEntity: Reservation::class, cascade: ["persist", "remove"])]
    private Collection $reservations;

    public function __construct()
    {
        $this->aviss = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    // Getters et Setters

    public function getId_v(): int
    {
        return $this->id_v;
    }

    public function setId_v(int $id_v): self
    {
        $this->id_v = $id_v;
        return $this;
    }

    public function getAviss(): Collection
    {
        return $this->aviss;
    }

    public function addAvis(Avis $avis): self
    {
        if (!$this->aviss->contains($avis)) {
            $this->aviss[] = $avis;
            $avis->setIdV($this); // Associer l'avis à la voiture
        }

        return $this;
    }

    public function removeAvis(Avis $avis): self
    {
        if ($this->aviss->removeElement($avis)) {
            // Vous n'avez pas besoin de définir l'id_v à null si vous gérez autrement la suppression
            // Vous pouvez laisser cette partie vide ou ajouter une logique spécifique si nécessaire
        }
    
        return $this;
    }
    public function getMarque()
    {
        return $this->marque;
    }

    public function setMarque($value)
    {
        $this->marque = $value;
    }
    // Méthodes pour Reservation sont aussi ici, comme dans la version précédente
}