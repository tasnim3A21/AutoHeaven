<?php
namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $note;

    #[ORM\Column(type: 'text')]
    private string $commentaire;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateavis;
    
    

    // Changer id_utilisateur en id (qui correspond à la colonne 'id' de la table 'user')
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id", nullable: false)]  // ici on utilise la colonne id
    private User $utilisateur;

    // Relation avec Voiture
    #[ORM\ManyToOne(targetEntity: Voiture::class)]
    #[ORM\JoinColumn(name: "id_v", referencedColumnName: "id_v", nullable: false, onDelete: "CASCADE")]
    private ?Voiture $idV = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function getCommentaire(): string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getDateavis(): \DateTimeInterface
    {
        return $this->dateavis;
    }

    public function setDateavis(\DateTimeInterface $dateavis): self
{
    // Ensure the date is a valid DateTimeInterface object
    if (is_string($dateavis)) {
        $dateavis = new \DateTime($dateavis);  // Convert string to DateTime if necessary
    }

    $this->dateavis = $dateavis;

    return $this;
}

    

    public function getUtilisateur(): User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(User $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getIdV(): ?Voiture
    {
        return $this->idV;
    }

    public function setIdV(?Voiture $idV): self
    {
        $this->idV = $idV;
        return $this;
    }
}
