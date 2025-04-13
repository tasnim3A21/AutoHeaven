<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use App\Entity\Lignecommande;

#[ORM\Entity]
class Commande
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]

    #[ORM\GeneratedValue(strategy: "AUTO")] // Add this to enable auto-increment

    private int $id_com;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "commandes")]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private User $id;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_com;

    #[ORM\Column(type: "string")]
    private string $status;

    #[ORM\Column(type: "float")]
    private float $montant_total;

    public function getId_com()
    {
        return $this->id_com;
    }

    public function setId_com($value)
    {
        $this->id_com = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getDate_com()
    {
        return $this->date_com;
    }

    public function setDate_com($value)
    {
        $this->date_com = $value;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($value)
    {
        $this->status = $value;
    }

    public function getMontant_total()
    {
        return $this->montant_total;
    }

    public function setMontant_total($value)
    {
        $this->montant_total = $value;
    }

    #[ORM\OneToMany(mappedBy: "idc", targetEntity: Lignecommande::class)]
    private Collection $lignecommandes;

        public function getLignecommandes(): Collection
        {
            return $this->lignecommandes;
        }
    
        public function addLignecommande(Lignecommande $lignecommande): self
        {
            if (!$this->lignecommandes->contains($lignecommande)) {
                $this->lignecommandes[] = $lignecommande;
                $lignecommande->setIdc($this);
            }
    
            return $this;
        }
    
        public function removeLignecommande(Lignecommande $lignecommande): self
        {
            if ($this->lignecommandes->removeElement($lignecommande)) {
                // set the owning side to null (unless already changed)
                if ($lignecommande->getIdc() === $this) {
                    $lignecommande->setIdc(null);
                }
            }
    
            return $this;
        }
}
