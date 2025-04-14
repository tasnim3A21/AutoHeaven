<?php

namespace App\Entity;

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
    #[Assert\NotBlank(message: "L'ID utilisateur ne peut pas être vide")]
    #[Assert\Positive(message: "L'ID utilisateur doit être un nombre positif")]
    private int $id_u;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "L'ID mécanicien ne peut pas être vide")]
    #[Assert\Positive(message: "L'ID mécanicien doit être un nombre positif")]
    private int $id_mec;

    #[ORM\Column(type: "string", length: 25)]
    #[Assert\NotBlank(message: "L'adresse ne peut pas être vide")]
    #[Assert\Length(
        min: 5,
        max: 25,
        minMessage: "L'adresse doit contenir au moins {{ limit }} caractères",
        maxMessage: "L'adresse ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $adresse;

    #[ORM\Column(type: "string", length: 100)]
    #[Assert\NotBlank(message: "La note ne peut pas être vide")]
    #[Assert\Length(
        max: 100,
        maxMessage: "La note ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $note;

    #[ORM\Column(type: "date")]
    #[Assert\NotBlank(message: "La date ne peut pas être vide")]
    #[Assert\Type("\DateTimeInterface")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "La date doit être aujourd'hui ou ultérieure"
    )]
    private \DateTimeInterface $date;

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank(message: "Le status ne peut pas être vide")]
    #[Assert\Choice(
        choices: ["en_cours_de_traitement", "confirmee", "rejetee"],
        message: "Le status doit être soit en cours de traitement, confirmée ou rejetée"
    )]
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

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "id_mec", referencedColumnName: "id")]
    private ?User $mecanicien = null;

    public function getMecanicien(): ?User
    {
        return $this->mecanicien;
    }

    public function setMecanicien(?User $mecanicien): self
    {
        $this->mecanicien = $mecanicien;
        return $this;
    }

}
