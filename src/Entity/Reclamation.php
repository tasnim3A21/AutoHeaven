<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private ?int $id_rec = null;

    #[ORM\Column(type: "integer")]
    private ?int $id = null; // Clé étrangère vers user(id)

    #[ORM\Column(type: "string", length: 50)]
    private ?string $titre = null;

    #[ORM\Column(type: "text")]
    private ?string $contenu = null;

    #[ORM\Column(type: "string", options: ["default" => "en_attente"])]
    private ?string $status = 'en_attente';

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $datecreation = null;

    #[ORM\OneToMany(mappedBy: "id_rec", targetEntity: Messagerie::class)]
    private Collection $messageries;

    public function __construct()
    {
        $this->messageries = new ArrayCollection();
    }

    public function getIdRec(): ?int
    {
        return $this->id_rec;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->datecreation;
    }

    public function setDatecreation(?\DateTimeInterface $datecreation): self
    {
        $this->datecreation = $datecreation;
        return $this;
    }

    public function getMessageries(): Collection
    {
        return $this->messageries;
    }

    public function addMessagerie(Messagerie $messagerie): self
    {
        if (!$this->messageries->contains($messagerie)) {
            $this->messageries[] = $messagerie;
            $messagerie->setIdRec($this);
        }
        return $this;
    }

    public function removeMessagerie(Messagerie $messagerie): self
    {
        if ($this->messageries->removeElement($messagerie)) {
            if ($messagerie->getIdRec() === $this) {
                $messagerie->setIdRec(null);
            }
        }
        return $this;
    }
}