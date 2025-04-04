<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Equipement;

#[ORM\Entity]
class Stock
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")] // Add this to enable auto-increment
    private int $id_s;

        #[ORM\ManyToOne(targetEntity: Equipement::class, inversedBy: "stocks")]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Equipement $id;

    #[ORM\Column(type: "integer")]
    #[ORM\Assert\NotBlank(message: "Quantité est obligatoire.")]
  
    private int $quantite;

    #[ORM\Column(type: "float")]
    #[ORM\Assert\NotBlank(message: "Prix de vente est obligatoire.")]
   
    private float $prixvente;

    public function getId_s()
    {
        return $this->id_s;
    }

    public function setId_s($value)
    {
        $this->id_s = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getQuantite()
    {
        return $this->quantite;
    }

    public function setQuantite($value)
    {
        $this->quantite = $value;
    }

    public function getPrixvente()
    {
        return $this->prixvente;
    }

    public function setPrixvente($value)
    {
        $this->prixvente = $value;
    }
}
