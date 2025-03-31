<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Equipement;

#[ORM\Entity]
class Panier
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_p;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "paniers")]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private User $id;

        #[ORM\ManyToOne(targetEntity: Equipement::class, inversedBy: "paniers")]
    #[ORM\JoinColumn(name: 'id_e', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Equipement $id_e;

    #[ORM\Column(type: "integer")]
    private int $quantite;

    public function getId_p()
    {
        return $this->id_p;
    }

    public function setId_p($value)
    {
        $this->id_p = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getId_e()
    {
        return $this->id_e;
    }

    public function setId_e($value)
    {
        $this->id_e = $value;
    }

    public function getQuantite()
    {
        return $this->quantite;
    }

    public function setQuantite($value)
    {
        $this->quantite = $value;
    }
}
