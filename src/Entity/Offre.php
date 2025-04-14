<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Equipement;
    
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]  // Add this line to auto-generate the ID
    #[ORM\Column(type: "integer")]
    private int $id_offre;


    #[ORM\ManyToOne(targetEntity: Equipement::class, inversedBy: "offres")]
    #[ORM\JoinColumn(name: 'id_equipement', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Equipement $equipement;

    #[ORM\Column(type: "string", length: 255)]
    private string $type_offre;  // Correctly named with underscore

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


/**
 * @Assert\Callback
 */
public function validateDates(ExecutionContextInterface $context): void
{
    if ($this->date_debut && $this->date_fin && $this->date_fin < $this->date_debut) {
        $context->buildViolation('La date de fin doit être postérieure à la date de début.')
            ->atPath('date_fin') // this links the error to the date_fin field
            ->addViolation();
    }
}


    // Correct getters and setters with snake_case
    public function getIdOffre()
    {
        return $this->id_offre;
    }

    public function setIdOffre($value)
    {
        $this->id_offre = $value;
    }

    public function getEquipement(): Equipement
    {
        return $this->equipement;
    }

    public function setEquipement(Equipement $equipement): self
    {
        $this->equipement = $equipement;
        return $this;
    }

    public function  getTypeOffre(): string
    {
        return $this->type_offre;  // Correct method name for type_offre
    }

    public function setTypeOffre(string $value): self
    {
        $this->type_offre = $value;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $value): self
    {
        $this->description = $value;
        return $this;
    }

    public function getTauxReduction(): float
    {
        return $this->taux_reduction;
    }

    public function setTauxReduction(float $value): self
    {
        $this->taux_reduction = $value;
        return $this;
    }

    public function getDateDebut(): \DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $value): self
    {
        $this->date_debut = $value;
        return $this;
    }

    public function getDateFin(): \DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $value): self
    {
        $this->date_fin = $value;
        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $value): self
    {
        $this->image = $value;
        return $this;
    }
}
