<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Panier;

#[ORM\Entity]
class Equipement
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 50)]
    private string $nom;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "text")]
    private string $image;

    #[ORM\Column(type: "string", length: 50)]
    private string $reference;

    #[ORM\Column(type: "string", length: 50)]
    private string $marque;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($value)
    {
        $this->image = $value;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function setReference($value)
    {
        $this->reference = $value;
    }

    public function getMarque()
    {
        return $this->marque;
    }

    public function setMarque($value)
    {
        $this->marque = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_equipement", targetEntity: Offre::class)]
    private Collection $offres;

        public function getOffres(): Collection
        {
            return $this->offres;
        }
    
        public function addOffre(Offre $offre): self
        {
            if (!$this->offres->contains($offre)) {
                $this->offres[] = $offre;
                $offre->setId_equipement($this);
            }
    
            return $this;
        }
    
        public function removeOffre(Offre $offre): self
        {
            if ($this->offres->removeElement($offre)) {
                // set the owning side to null (unless already changed)
                if ($offre->getId_equipement() === $this) {
                    $offre->setId_equipement(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "id", targetEntity: Stock::class)]
    private Collection $stocks;

        public function getStocks(): Collection
        {
            return $this->stocks;
        }
    
        public function addStock(Stock $stock): self
        {
            if (!$this->stocks->contains($stock)) {
                $this->stocks[] = $stock;
                $stock->setId($this);
            }
    
            return $this;
        }
    
        public function removeStock(Stock $stock): self
        {
            if ($this->stocks->removeElement($stock)) {
                // set the owning side to null (unless already changed)
                if ($stock->getId() === $this) {
                    $stock->setId(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "id_e", targetEntity: Lignecommande::class)]
    private Collection $lignecommandes;

        public function getLignecommandes(): Collection
        {
            return $this->lignecommandes;
        }
    
        public function addLignecommande(Lignecommande $lignecommande): self
        {
            if (!$this->lignecommandes->contains($lignecommande)) {
                $this->lignecommandes[] = $lignecommande;
                $lignecommande->setId_e($this);
            }
    
            return $this;
        }
    
        public function removeLignecommande(Lignecommande $lignecommande): self
        {
            if ($this->lignecommandes->removeElement($lignecommande)) {
                // set the owning side to null (unless already changed)
                if ($lignecommande->getId_e() === $this) {
                    $lignecommande->setId_e(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "id_e", targetEntity: Panier::class)]
    private Collection $paniers;
}
