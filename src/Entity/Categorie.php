<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Voiture;

#[ORM\Entity]
class Categorie
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_c;

    #[ORM\Column(type: "string", length: 45)]
    private string $type;

    #[ORM\Column(type: "string", length: 50)]
    private string $type_carburant;

    #[ORM\Column(type: "string", length: 50)]
    private string $type_utilisation;

    #[ORM\Column(type: "integer")]
    private int $nbr_porte;

    #[ORM\Column(type: "string", length: 50)]
    private string $transmission;

    public function getId_c()
    {
        return $this->id_c;
    }

    public function setId_c($value)
    {
        $this->id_c = $value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($value)
    {
        $this->type = $value;
    }

    public function getType_carburant()
    {
        return $this->type_carburant;
    }

    public function setType_carburant($value)
    {
        $this->type_carburant = $value;
    }

    public function getType_utilisation()
    {
        return $this->type_utilisation;
    }

    public function setType_utilisation($value)
    {
        $this->type_utilisation = $value;
    }

    public function getNbr_porte()
    {
        return $this->nbr_porte;
    }

    public function setNbr_porte($value)
    {
        $this->nbr_porte = $value;
    }

    public function getTransmission()
    {
        return $this->transmission;
    }

    public function setTransmission($value)
    {
        $this->transmission = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_c", targetEntity: Voiture::class)]
    private Collection $voitures;

        public function getVoitures(): Collection
        {
            return $this->voitures;
        }
    
        public function addVoiture(Voiture $voiture): self
        {
            if (!$this->voitures->contains($voiture)) {
                $this->voitures[] = $voiture;
                $voiture->setId_c($this);
            }
    
            return $this;
        }
    
        public function removeVoiture(Voiture $voiture): self
        {
            if ($this->voitures->removeElement($voiture)) {
                // set the owning side to null (unless already changed)
                if ($voiture->getId_c() === $this) {
                    $voiture->setId_c(null);
                }
            }
    
            return $this;
        }
}
