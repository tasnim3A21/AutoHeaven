<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Voiture;
use Doctrine\Common\Collections\Collection;
use App\Entity\Mention_j_aime;

#[ORM\Entity]
class Avis
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_a;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "aviss")]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private User $id;

        #[ORM\ManyToOne(targetEntity: Voiture::class, inversedBy: "aviss")]
    #[ORM\JoinColumn(name: 'id_v', referencedColumnName: 'id_v', onDelete: 'CASCADE')]
    private Voiture $id_v;

    #[ORM\Column(type: "integer")]
    private int $note;

    #[ORM\Column(type: "text")]
    private string $commentaire;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $dateavis;

    public function getId_a()
    {
        return $this->id_a;
    }

    public function setId_a($value)
    {
        $this->id_a = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getId_v()
    {
        return $this->id_v;
    }

    public function setId_v($value)
    {
        $this->id_v = $value;
    }

    public function getNote()
    {
        return $this->note;
    }

    public function setNote($value)
    {
        $this->note = $value;
    }

    public function getCommentaire()
    {
        return $this->commentaire;
    }

    public function setCommentaire($value)
    {
        $this->commentaire = $value;
    }

    public function getDateavis()
    {
        return $this->dateavis;
    }

    public function setDateavis($value)
    {
        $this->dateavis = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_a", targetEntity: Mention_j_aime::class)]
    private Collection $mention_j_aimes;

        public function getMention_j_aimes(): Collection
        {
            return $this->mention_j_aimes;
        }
    
        public function addMention_j_aime(Mention_j_aime $mention_j_aime): self
        {
            if (!$this->mention_j_aimes->contains($mention_j_aime)) {
                $this->mention_j_aimes[] = $mention_j_aime;
                $mention_j_aime->setId_a($this);
            }
    
            return $this;
        }
    
        public function removeMention_j_aime(Mention_j_aime $mention_j_aime): self
        {
            if ($this->mention_j_aimes->removeElement($mention_j_aime)) {
                // set the owning side to null (unless already changed)
                if ($mention_j_aime->getId_a() === $this) {
                    $mention_j_aime->setId_a(null);
                }
            }
    
            return $this;
        }
}
