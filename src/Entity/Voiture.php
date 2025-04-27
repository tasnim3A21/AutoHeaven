<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Categorie;
use Doctrine\Common\Collections\Collection;
use App\Entity\Reservation;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
class Voiture
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id_v;

        #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: "voitures")]
    #[ORM\JoinColumn(name: 'id_c', referencedColumnName: 'id_c', onDelete: 'CASCADE')]
    private Categorie $id_c;

    #[ORM\Column(type: "string", length: 45)]
    #[Assert\NotBlank(message: "La marque est obligatoire.")]
    #[Assert\Length(max: 45, maxMessage: "La marque ne doit pas dépasser 45 caractères.")]
    private ?string $marque = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    private ?string $description = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Le kilométrage est obligatoire.")]
#[Assert\PositiveOrZero(message: "Le kilométrage doit être un nombre positif.")]
    private ?int $kilometrage = null;

    #[ORM\Column(type: "string", length: 45)]
    #[Assert\NotBlank(message: "La couleur est obligatoire.")]
#[Assert\Length(max: 45, maxMessage: "La couleur ne doit pas dépasser 45 caractères.")]
    private ?string $couleur = null;

    #[ORM\Column(type: "float")]
    #[Assert\NotBlank(message: "Le prix est obligatoire.")]
#[Assert\Positive(message: "Le prix doit être un nombre positif.")]
    private ?float $prix = null;

    #[ORM\Column(type: "text")]
   // #[Assert\NotBlank(message: "L'image est obligatoire.")]
    private string $image;

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank(message: "La disponibilité est obligatoire.")]
    #[Assert\Choice(choices: ["oui", "non"], message: "La disponibilité doit être 'oui' ou 'non'.")]
    private ?string $disponibilite = null;

    private ?File $imageFile = null;

    /**
 * @return File|null
 */
public function getImageFile(): ?File
{
    return $this->imageFile;
}

/**
 * @param File|UploadedFile|null $imageFile
 */
public function setImageFile(?File $imageFile): void
{
    $this->imageFile = $imageFile;
}
public function getIdV()
{
    return $this->id_v;
}

public function setIdV($value)
{
    $this->id_v = $value;
}

    public function getId_v()
    {
        return $this->id_v;
    }

    public function setId_v($value)
    {
        $this->id_v = $value;
    }
     
    public function getIdC()
    {
        return $this->id_c;
    }


    public function setIdC($value)
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
                $avis->setIdV($this);
            }
    
            return $this;
        }
    
        public function removeAvis(Avis $avis): self
        {
            if ($this->aviss->removeElement($avis)) {
                // set the owning side to null (unless already changed)
                if ($avis->getIdV() === $this) {
                    $avis->setIdV(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "id_v", targetEntity: Reservation::class)]
    private Collection $reservations;
}