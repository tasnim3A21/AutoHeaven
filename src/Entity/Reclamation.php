<?php



// src/Entity/Reclamation.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="reclamation")
 */
#[ORM\Entity]
#[ORM\Table(name: "reclamation")]
class Reclamation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private ?int $id_rec = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $id = null; // Clé étrangère vers user(id)

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Le champ titre est requis.")
     */
    #[ORM\Column(type: "string", length: 50)]
    private ?string $titre = null;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Le champ contenu est requis.")
     */
    #[ORM\Column(type: "text")]
    private ?string $contenu = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $image = null; // Champ pour stocker le nom ou le chemin de l'image

    /**
     * @Assert\File(
     *     maxSize="5M",
     *     mimeTypes={"image/jpeg", "image/png", "image/gif"},
     *     mimeTypesMessage="Veuillez uploader une image valide (JPEG, PNG, GIF)."
     * )
     */
    private ?File $imageFile = null; // Propriété temporaire pour l'upload

    /**
     * @ORM\Column(type="string", length=20, options={"default":"en_attente"})
     */
    #[ORM\Column(type: "string", length: 20, options: ["default" => "en_attente"])]
    private ?string $status = 'en_attente';

    /**
     * @ORM\Column(type="datetime")
     */
    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $datecreation = null;

    /**
     * @ORM\OneToMany(mappedBy="id_rec", targetEntity=Messagerie::class, cascade={"persist", "remove"})
     */
    #[ORM\OneToMany(mappedBy: "id_rec", targetEntity: Messagerie::class, cascade: ["persist", "remove"])]
    private Collection $messageries;

    public function __construct()
    {
        $this->messageries = new ArrayCollection();
        $this->datecreation = new \DateTime();
        $this->status = 'en_attente';
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;
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

    /**
     * @return Collection<int, Messagerie>
     */
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

    public function __toString(): string
    {
        return $this->titre ?? 'Réclamation #' . $this->id_rec;
    }
}


