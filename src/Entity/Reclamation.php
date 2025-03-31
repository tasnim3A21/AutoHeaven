<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use App\Entity\Messagerie;

#[ORM\Entity]
class Reclamation
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_rec;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "reclamations")]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private User $id;

    #[ORM\Column(type: "string", length: 50)]
    private string $titre;

    #[ORM\Column(type: "text")]
    private string $contenu;

    #[ORM\Column(type: "string")]
    private string $status;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $datecreation;

    public function getId_rec()
    {
        return $this->id_rec;
    }

    public function setId_rec($value)
    {
        $this->id_rec = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($value)
    {
        $this->titre = $value;
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function setContenu($value)
    {
        $this->contenu = $value;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($value)
    {
        $this->status = $value;
    }

    public function getDatecreation()
    {
        return $this->datecreation;
    }

    public function setDatecreation($value)
    {
        $this->datecreation = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_rec", targetEntity: Messagerie::class)]
    private Collection $messageries;

        public function getMessageries(): Collection
        {
            return $this->messageries;
        }
    
        public function addMessagerie(Messagerie $messagerie): self
        {
            if (!$this->messageries->contains($messagerie)) {
                $this->messageries[] = $messagerie;
                $messagerie->setId_rec($this);
            }
    
            return $this;
        }
    
        public function removeMessagerie(Messagerie $messagerie): self
        {
            if ($this->messageries->removeElement($messagerie)) {
                // set the owning side to null (unless already changed)
                if ($messagerie->getId_rec() === $this) {
                    $messagerie->setId_rec(null);
                }
            }
    
            return $this;
        }
}
