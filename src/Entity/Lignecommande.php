<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Commande;

#[ORM\Entity]
class Lignecommande
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_l;

        #[ORM\ManyToOne(targetEntity: Equipement::class, inversedBy: "lignecommandes")]
    #[ORM\JoinColumn(name: 'id_e', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Equipement $id_e;

        #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: "lignecommandes")]
    #[ORM\JoinColumn(name: 'idc', referencedColumnName: 'id_com', onDelete: 'CASCADE')]
    private Commande $idc;

    #[ORM\Column(type: "integer")]
    private int $quantite;

    #[ORM\Column(type: "float")]
    private float $prix_unitaire;

    public function getId_l()
    {
        return $this->id_l;
    }

    public function setId_l($value)
    {
        $this->id_l = $value;
    }

    public function getId_e()
    {
        return $this->id_e;
    }

    public function setId_e($value)
    {
        $this->id_e = $value;
    }

    public function getIdc()
    {
        return $this->idc;
    }

    public function setIdc($value)
    {
        $this->idc = $value;
    }

    public function getQuantite()
    {
        return $this->quantite;
    }

    public function setQuantite($value)
    {
        $this->quantite = $value;
    }

    public function getPrix_unitaire()
    {
        return $this->prix_unitaire;
    }

    public function setPrix_unitaire($value)
    {
        $this->prix_unitaire = $value;
    }
}
