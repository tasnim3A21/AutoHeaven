<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Assert\Range(min:1, max:5)]
    private int $note;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Length(min:5, max:500)]
    private string $commentaire;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\LessThanOrEqual('today')]
    private \DateTimeInterface $dateavis;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id", nullable: false)]
    private User $utilisateur;
    #[ORM\ManyToOne(targetEntity: Voiture::class)]
    #[ORM\JoinColumn(name: "id_v", referencedColumnName: "id_v", nullable: false)]
    private Voiture $voiture;


    public function getId(): ?int { return $this->id; }
    public function getNote(): int { return $this->note; }
    public function setNote(int $note): self { $this->note = $note; return $this; }
    public function getCommentaire(): string { return $this->commentaire; }
    public function setCommentaire(string $c): self { $this->commentaire = $c; return $this; }
    public function getDateavis(): \DateTimeInterface { return $this->dateavis; }
    public function setDateavis(\DateTimeInterface $d): self { $this->dateavis = $d; return $this; }
    public function getUtilisateur(): User { return $this->utilisateur; }
    public function setUtilisateur(User $u): self { $this->utilisateur = $u; return $this; }
}