<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
class Camion_remorquage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id_cr;

    #[ORM\Column(type:"string", length: 25)]
    #[Assert\NotBlank(message: "Le nom de l'agence ne peut pas être vide")]
    private string $nom_agence = '';  // Initialize with empty string

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->nom_agence = '';  // Initialize in constructor as well
    }

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank(message: "Le modèle ne peut pas être vide")]
    #[Assert\Choice(choices: ["Standard", "XL"], message: "Le modèle doit être soit Standard soit XL")]
    private string $modele;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "L'année ne peut pas être vide")]
    #[Assert\Range(
        min: 1990,
        max: 2025,
        notInRangeMessage: "L'année doit être entre {{ min }} et {{ max }}"
    )]
    private int $annee;

    #[ORM\Column(type: "string", length: 8)]
    #[Assert\NotBlank(message: "Le numéro de téléphone ne peut pas être vide")]
    #[Assert\Length(
        exactly: 8,
        exactMessage: "Le numéro de téléphone doit contenir exactement {{ limit }} chiffres"
    )]
    #[Assert\Regex(
        pattern: "/^[0-9]+$/",
        message: "Le numéro de téléphone ne doit contenir que des chiffres"
    )]
    private string $num_tel;

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank(message: "Le statut ne peut pas être vide")]
    #[Assert\Choice(
        choices: ["Disponible", "Non Disponible"],
        message: "Le statut doit être soit Disponible soit Non Disponible"
    )]

    private string $statut;

    public function getId_cr()
    {
        return $this->id_cr;
    }


    public function getModele()
    {
        return $this->modele;
    }

    public function setModele($value)
    {
        $this->modele = $value;
    }

    public function getAnnee()
    {
        return $this->annee;
    }

    public function setAnnee($value)
    {
        $this->annee = $value;
    }


    public function getNumTel()

    {
        return $this->num_tel;
    }


    public function setNumTel($value)

    {
        $this->num_tel = $value;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($value)
    {
        $this->statut = $value;
    }


    public function getNomAgence(): ?string
    {
        return $this->nom_agence;
    }

    public function setNomAgence(string $nom_agence): static
    {
        $this->nom_agence = $nom_agence;
        return $this;
    }

    #[ORM\OneToMany(mappedBy: 'camionRemorquage', targetEntity: Res_remorquage::class)]
    private Collection|array $reservations;

    /**
     * @return Collection<int, Res_remorquage>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Res_remorquage $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setCamionRemorquage($this);
        }
        return $this;
    }

    public function removeReservation(Res_remorquage $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getCamionRemorquage() === $this) {
                $reservation->setCamionRemorquage(null);
            }
        }
        return $this;
    }
}
