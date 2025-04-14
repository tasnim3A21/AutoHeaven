<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Voiture_remorquage
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_vr;

    #[ORM\Column(type: "string", length: 50)]
    private string $marque;

    #[ORM\Column(type: "string", length: 50)]
    private string $modele;

    #[ORM\Column(type: "integer")]
    private int $annee;

    #[ORM\Column(type: "string", length: 20)]
    private string $num_agence;

    #[ORM\Column(type: "string", length: 20)]
    private string $statut;

    public function getId_vr()
    {
        return $this->id_vr;
    }

    public function setId_vr($value)
    {
        $this->id_vr = $value;
    }

    public function getMarque()
    {
        return $this->marque;
    }

    public function setMarque($value)
    {
        $this->marque = $value;
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

    public function getNum_agence()
    {
        return $this->num_agence;
    }

    public function setNum_agence($value)
    {
        $this->num_agence = $value;
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
