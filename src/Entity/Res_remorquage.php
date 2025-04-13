<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Res_remorquage
{

    #[ORM\Id]
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

    public function getId_rem()
    {
        return $this->id_rem;
    }

    public function setId_rem($value)
    {
        $this->id_rem = $value;
    }

    public function getId_cr()
    {
        return $this->id_cr;
    }

    public function setId_cr($value)
    {
        $this->id_cr = $value;
    }

    public function getId_u()
    {
        return $this->id_u;
    }

    public function setId_u($value)
    {
        $this->id_u = $value;
    }

    public function getPoint_ramassage()
    {
        return $this->point_ramassage;
    }

    public function setPoint_ramassage($value)
    {
        $this->point_ramassage = $value;
    }

    public function getPoint_depot()
    {
        return $this->point_depot;
    }

    public function setPoint_depot($value)
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
}
