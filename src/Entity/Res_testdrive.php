<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Res_testdrive
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id_td;

    #[ORM\Column(type: "integer")]
    private int $id_u;

    #[ORM\Column(type: "integer")]
    private int $id_v;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date;

    #[ORM\Column(type: "string")]
    private string $status;

    public function getId_td()
    {
        return $this->id_td;
    }

    public function setId_td($value)
    {
        $this->id_td = $value;
    }

    public function getIdU()
    {
        return $this->id_u;
    }

    public function setIdU($value)
    {
        $this->id_u = $value;
    }

    public function getIdV()
    {
        return $this->id_v;
    }

    public function setIdV($value)
    {
        $this->id_v = $value;
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
