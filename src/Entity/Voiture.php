<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Categorie;
use Doctrine\Common\Collections\Collection;
use App\Entity\Reservation;

#[ORM\Entity]
class Voiture
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_v;

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

    public function getId_v()
    {
        return $this->id_v;
    }

    public function setId_v($value)
    {
        $this->id_v = $value;
    }

    public function getId_c()
    {
        return $this->id_c;
    }

    public function setId_c($value)
    {
        $this->id_c = $value;
    }

    public function getMarque()
    {
        return $this->marque;
    }

    public function setMarque($value)
    {
        $this->marque = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getKilometrage()
    {
        return $this->kilometrage;
    }

    public function setKilometrage($value)
    {
        $this->kilometrage = $value;
    }

    public function getCouleur()
    {
        return $this->couleur;
    }

    public function setCouleur($value)
    {
        $this->couleur = $value;
    }

    public function getPrix()
    {
        return $this->prix;
    }

    public function setPrix($value)
    {
        $this->prix = $value;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($value)
    {
        $this->image = $value;
    }

    public function getDisponibilite()
    {
        return $this->disponibilite;
    }

    public function setDisponibilite($value)
    {
        $this->disponibilite = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_v", targetEntity: Avis::class)]
    private Collection $aviss;

        public function getAviss(): Collection
        {
            return $this->aviss;
        }
    
        public function addAvis(Avis $avis): self
        {
            if (!$this->aviss->contains($avis)) {
                $this->aviss[] = $avis;
                $avis->setId_v($this);
            }
    
            return $this;
        }
    
        public function removeAvis(Avis $avis): self
        {
            if ($this->aviss->removeElement($avis)) {
                // set the owning side to null (unless already changed)
                if ($avis->getId_v() === $this) {
                    $avis->setId_v(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "id_v", targetEntity: Reservation::class)]
    private Collection $reservations;
}
