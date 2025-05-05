<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

#[ORM\Entity]
class Reservation
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_r;

    #[ORM\ManyToOne(targetEntity: Voiture::class, inversedBy: "reservations")]
    #[ORM\JoinColumn(name: 'id_v', referencedColumnName: 'id_v', onDelete: 'CASCADE')]
    private Voiture $id_v;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "reservations")]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private User $id;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank(message: "La date ne peut pas être vide")]
    #[Assert\Type("\DateTimeInterface")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "La date doit être aujourd'hui ou ultérieure"
    )]
    private ?\DateTimeInterface $date_res=null;

    #[ORM\Column(type: "string")]
    private string $status;

    public function getId_r()
    {
        return $this->id_r;
    }

    public function setId_r($value)
    {
        $this->id_r = $value;
    }

    public function getId_v()
    {
        return $this->id_v;
    }

    public function setId_v(Voiture $value)
    {
        $this->id_v = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getDate_res()
    {
        return $this->date_res;
    }

    public function setDate_res($value)
    {
        $this->date_res = $value;
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
