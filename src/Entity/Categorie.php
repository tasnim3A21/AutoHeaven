<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Voiture;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Categorie
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id_c;

    #[ORM\Column(type: "string", length: 45)]
    #[Assert\NotBlank(message: "Le type est requis.")]
    #[Assert\Length(max: 45, maxMessage: "Le type ne peut pas dépasser 45 caractères.")]
    private ?string $type = null;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: "Le type de carburant est requis.")]
    #[Assert\Length(max: 50, maxMessage: "Le type de carburant ne peut pas dépasser 50 caractères.")]
    private ?string $type_carburant = null;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: "Le type d'utilisation est requis.")]
    #[Assert\Length(max: 50, maxMessage: "Le type d'utilisation ne peut pas dépasser 50 caractères.")]
    private ?string $type_utilisation = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotNull(message: "Le nombre de portes est requis.")]
    #[Assert\Positive(message: "Le nombre de portes doit être un entier positif.")]
    private ?int $nbr_porte = null;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: "La transmission est requise.")]
    #[Assert\Length(max: 50, maxMessage: "La transmission ne peut pas dépasser 50 caractères.")]
    private ?string $transmission = null;

    public function getId()
    {
        return $this->id_c;
    }

    public function setId($value)
    {
        $this->id_c = $value;
    }
    
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

    public function getTypeCarburant()
    {
        return $this->type_carburant;
    }

    public function setTypeCarburant($value)
    {
        $this->type_carburant = $value;
    }

   /* public function getType_utilisation()
    {
        return $this->type_utilisation;
    }

    public function setType_utilisation($value)
    {
        $this->type_utilisation = $value;
    }*/
    public function getTypeUtilisation()
    {
        return $this->type_utilisation;
    }

    public function setTypeUtilisation($value)
    {
        $this->type_utilisation = $value;
    }
    public function getNbrPorte()
    {
        return $this->nbr_porte;
    }

    public function setNbrPorte($value)
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