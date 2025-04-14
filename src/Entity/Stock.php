<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Equipement;

use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity]
class Stock
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]


    #[ORM\GeneratedValue(strategy: "AUTO")]
    private int $id_s;

    #[ORM\OneToOne(targetEntity: Equipement::class, inversedBy: "stock")]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?Equipement $equipement = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Quantité est obligatoire.")]
    #[Assert\Positive(message: "La quantité doit être positive.")]
    private int $quantite;

    #[ORM\Column(type: "float")]
    #[Assert\NotBlank(message: "Prix de vente est obligatoire.")]
    #[Assert\Positive(message: "Le prix de vente doit être positif.")]
    private float $prixvente;

    public function getIdS(): ?int

    {
        return $this->id_s;
    }


    public function setIdS(int $id_s): self
    {
        $this->id_s = $id_s;
        return $this;
    }

    public function getEquipement(): ?Equipement
    {
        return $this->equipement;
    }

    public function setEquipement(?Equipement $equipement): self
    {
        $this->equipement = $equipement;
        return $this;
    }

    public function getQuantite(): int


    {
        return $this->quantite;
    }




    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getPrixvente(): float



    {
        return $this->prixvente;
    }




    public function setPrixvente(float $prixvente): self
    {
        $this->prixvente = $prixvente;
        return $this;
    }
}


