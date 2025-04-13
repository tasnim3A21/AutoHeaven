<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Service_remorquage
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_service;

    #[ORM\Column(type: "integer")]
    private int $id_vr;

    #[ORM\Column(type: "string", length: 100)]
    private string $nom_chauffeur;

    #[ORM\Column(type: "string", length: 100)]
    private string $lieu;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_service;

    #[ORM\Column(type: "string", length: 20)]
    private string $statut;

    public function getId_service()
    {
        return $this->id_service;
    }

    public function setId_service($value)
    {
        $this->id_service = $value;
    }

    public function getId_vr()
    {
        return $this->id_vr;
    }

    public function setId_vr($value)
    {
        $this->id_vr = $value;
    }

    public function getNom_chauffeur()
    {
        return $this->nom_chauffeur;
    }

    public function setNom_chauffeur($value)
    {
        $this->nom_chauffeur = $value;
    }

    public function getLieu()
    {
        return $this->lieu;
    }

    public function setLieu($value)
    {
        $this->lieu = $value;
    }

    public function getDate_service()
    {
        return $this->date_service;
    }

    public function setDate_service($value)
    {
        $this->date_service = $value;
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
