<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity]
class Res_testdrive
{

    #[ORM\Id]

    #[ORM\GeneratedValue]

    #[ORM\Column(type: "integer")]
    private int $id_td;

    #[ORM\Column(type: "integer")]
    //#[Assert\NotBlank(message: "L'ID utilisateur ne peut pas être vide")]
    private int $id_u;

    #[ORM\Column(type: "integer")]
    //#[Assert\NotBlank(message: "L'ID véhicule ne peut pas être vide")]
    private int $id_v;

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
    private string $status = "en_cours_de_traitement";

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date)
    {
        $this->date = $date;
        return $this;
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
    #[ORM\JoinColumn(name: 'id_u', referencedColumnName: 'id')]
    #[Assert\NotBlank(message: "Le client ne peut pas être vide")]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Voiture::class)]
    #[ORM\JoinColumn(name: 'id_v', referencedColumnName: 'id_v')]
    #[Assert\NotBlank(message: "La voiture ne peut pas être vide")]
    private ?Voiture $voiture = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function setVoiture(?Voiture $voiture): self
    {
        $this->voiture = $voiture;
        return $this;
    }
}
