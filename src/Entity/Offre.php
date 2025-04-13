<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Equipement;

#[ORM\Entity]
class Offre
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_offre;

        #[ORM\ManyToOne(targetEntity: Equipement::class, inversedBy: "offres")]
    #[ORM\JoinColumn(name: 'id_equipement', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Equipement $id_equipement;

    #[ORM\Column(type: "string", length: 255)]
    private string $type_offre;

    #[ORM\Column(type: "string", length: 255)]
    private string $description;

    #[ORM\Column(type: "float")]
    private float $taux_reduction;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_debut;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_fin;

    #[ORM\Column(type: "string", length: 255)]
    private string $image;

    public function getId_offre()
    {
        return $this->id_offre;
    }

    public function setId_offre($value)
    {
        $this->id_offre = $value;
    }

    public function getId_equipement()
    {
        return $this->id_equipement;
    }

    public function setId_equipement($value)
    {
        $this->id_equipement = $value;
    }

    public function getType_offre()
    {
        return $this->type_offre;
    }

    public function setType_offre($value)
    {
        $this->type_offre = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getTaux_reduction()
    {
        return $this->taux_reduction;
    }

    public function setTaux_reduction($value)
    {
        $this->taux_reduction = $value;
    }

    public function getDate_debut()
    {
        return $this->date_debut;
    }

    public function setDate_debut($value)
    {
        $this->date_debut = $value;
    }

    public function getDate_fin()
    {
        return $this->date_fin;
    }

    public function setDate_fin($value)
    {
        $this->date_fin = $value;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($value)
    {
        $this->image = $value;
    }
}
