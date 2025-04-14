<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Res_mecanicien
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id_res_m;

    #[ORM\Column(type: "integer")]
    private int $id_u;

    #[ORM\Column(type: "integer")]
    private int $id_mec;

    #[ORM\Column(type: "string", length: 25)]
    private string $adresse;

    #[ORM\Column(type: "string", length: 100)]
    private string $note;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date;

    #[ORM\Column(type: "string")]
    private string $status;

    public function getId_res_m()
    {
        return $this->id_res_m;
    }

    public function getIdU()
    {
        return $this->id_u;
    }

    public function setIdU($value)
    {
        $this->id_u = $value;
    }

    public function getIdMec()
    {
        return $this->id_mec;
    }

    public function setIdMec($value)
    {
        $this->id_mec = $value;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function setAdresse($value)
    {
        $this->adresse = $value;
    }

    public function getNote()
    {
        return $this->note;
    }

    public function setNote($value)
    {
        $this->note = $value;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($value)
    {
        $this->date = $value;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($value)
    {
        $this->status = $value;
    }
}
