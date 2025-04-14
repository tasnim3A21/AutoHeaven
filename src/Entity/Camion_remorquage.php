<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Camion_remorquage
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id_cr;

    #[ORM\Column(type: "string")]
    private string $modele;

    #[ORM\Column(type: "integer")]
    private int $annee;

    #[ORM\Column(type: "string", length: 8)]
    private string $num_tel;

    #[ORM\Column(type: "string")]
    private string $statut;

    public function getId_cr()
    {
        return $this->id_cr;
    }

    public function getModele()
    {
        return $this->modele;
    }

    public function setModele($value)
    {
        $this->modele = $value;
    }

    public function getAnnee()
    {
        return $this->annee;
    }

    public function setAnnee($value)
    {
        $this->annee = $value;
    }

    public function getNumTel()
    {
        return $this->num_tel;
    }

    public function setNumTel($value)
    {
        $this->num_tel = $value;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($value)
    {
        $this->statut = $value;
    }
}
