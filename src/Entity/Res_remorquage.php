<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity]
class Res_remorquage
{

    #[ORM\Id]

    #[ORM\GeneratedValue]

    #[ORM\Column(type: "integer")]
    private int $id_rem;

    #[ORM\Column(type: "integer")]
    //#[Assert\NotBlank(message: "L'ID du camion ne peut pas être vide")]
    private int $id_cr;

    #[ORM\Column(type: "integer")]
    //#[Assert\NotBlank(message: "L'ID utilisateur ne peut pas être vide")]
    private int $id_u;

    #[ORM\Column(type: "string", nullable: true)]
    #[Assert\NotBlank(message: "Le point de ramassage ne peut pas être vide")]
    #[Assert\Length(
        min: 5,
        max: 100,
        minMessage: "Le point de ramassage doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le point de ramassage ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $point_ramassage = null;

    #[ORM\Column(type: "string", nullable: true)]
    #[Assert\NotBlank(message: "Le point de dépôt ne peut pas être vide")]
    #[Assert\Length(
        min: 5,
        max: 100,
        minMessage: "Le point de dépôt doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le point de dépôt ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $point_depot = null;

    #[ORM\Column(type: "date", nullable:true)]
    #[Assert\NotBlank(message: "La date ne peut pas être vide")]
    #[Assert\Type("\DateTimeInterface")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "La date doit être aujourd'hui ou ultérieure"
    )]
    private ?\DateTimeInterface $date=null;

    #[ORM\Column(type: "string")]
    /*#[Assert\NotBlank(message: "Le status ne peut pas être vide")]
    #[Assert\Choice(
        choices: ["en_cours_de_traitement", "confirmee", "rejetee"],
        message: "Le status doit être soit en cours de traitement, confirmée ou rejetée"
    )]*/
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

    #[ORM\ManyToOne(targetEntity: Camion_remorquage::class)]
    #[ORM\JoinColumn(name: 'id_cr', referencedColumnName: 'id_cr')]
    #[Assert\NotBlank(message: "Le camion ne peut pas être vide")]
    private ?Camion_remorquage $camionRemorquage = null;

    public function getCamionRemorquage(): ?Camion_remorquage
    {
        return $this->camionRemorquage;
    }

    public function setCamionRemorquage(?Camion_remorquage $camionRemorquage): self
    {
        $this->camionRemorquage = $camionRemorquage;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_u', referencedColumnName: 'id')]
    #[Assert\NotBlank(message: "Le client ne peut pas être vide")]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

}
