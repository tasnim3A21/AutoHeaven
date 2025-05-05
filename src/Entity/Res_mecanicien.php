<?php

namespace App\Entity;

// Assert is already imported below, removing duplicate import
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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

    #[ORM\Column(type: "string", length: 25, nullable: true)]
    #[Assert\NotBlank(message: "L'adresse ne peut pas être vide")]
    #[Assert\Length(
        min: 5,
        max: 25,
        minMessage: "L'adresse doit contenir au moins {{ limit }} caractères",
        maxMessage: "L'adresse ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $adresse = null;

    #[ORM\Column(type: "string", length: 100)]
    #[Assert\Length(
        max: 250,
        maxMessage: "La note ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $note = null;

    #[ORM\Column(type: "date", nullable:true)]
    #[Assert\NotBlank(message: "La date ne peut pas être vide")]
    #[Assert\Type("\DateTimeInterface")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "La date doit être aujourd'hui ou ultérieure"
    )]
    private ?\DateTimeInterface $date=null;

    #[ORM\Column(type: "string")]
    //#[Assert\NotBlank(message: "Le status ne peut pas être vide")]
    //#[Assert\Choice(
    //    choices: ["en_cours_de_traitement", "confirmee", "rejetee"],
    //    message: "Le status doit être soit en cours de traitement, confirmée ou rejetée"
    //)]
    private string $status;

    public function getId_res_m()
    {
        return $this->id_res_m;
    }
    public function setIdU($id_u)
    {
        $this->id_u = $id_u;
    }
    public function getIdU()
    {
        return $this->id_u;
    }

    public function getIdMec()
    {
        return $this->id_mec;  
    }
    public function setIdMec($id_mec)
    {
        $this->id_mec = $id_mec;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }
    public function setAdresse($value)
    {
        $this->adresse = $value;
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }
    public function setNote(?string $value): void
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

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_u', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotBlank(message: "Le client ne peut pas être vide")]
    private ?User $client = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_mec', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotBlank(message: "Le mécanicien ne peut pas être vide")]
    private ?User $mecanicien = null;

    public function getClient(): ?User
    {
        return $this->client;
    }
    public function setClient(User $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getMecanicien(): ?User
    {
        return $this->mecanicien;
    }

    public function setMecanicien(User $mecanicien): self
    {
        $this->mecanicien = $mecanicien;
        return $this;
    }
}
