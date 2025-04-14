<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Res_remorquage
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id_rem;

    #[ORM\Column(type: "integer")]
    private int $id_cr;

    #[ORM\Column(type: "integer")]
    private int $id_u;

    #[ORM\Column(type: "string", length: 25)]
    private string $point_ramassage;

    #[ORM\Column(type: "string", length: 25)]
    private string $point_depot;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date;

    #[ORM\Column(type: "string")]
    private string $status;

    public function getId_rem()
    {
        return $this->id_rem;
    }

    public function setId_rem($value)
    {
        $this->id_rem = $value;
    }

    public function getIdCr()
    {
        return $this->id_cr;
    }

    public function setIdCr($value)
    {
        $this->id_cr = $value;
    }

    public function getIdU()
    {
        return $this->id_u;
    }

    public function setIdU($value)
    {
        $this->id_u = $value;
    }

    public function getPointRamassage()
    {
        return $this->point_ramassage;
    }

    public function setPointRamassage($value)
    {
        $this->point_ramassage = $value;
    }

    public function getPointDepot()
    {
        return $this->point_depot;
    }

    public function setPointDepot($value)
    {
        $this->point_depot = $value;
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
