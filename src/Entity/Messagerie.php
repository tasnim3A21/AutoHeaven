<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
class Messagerie
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer", name: "id_m")]
    private ?int $id_m = null;

    #[ORM\ManyToOne(targetEntity: Reclamation::class, inversedBy: "messageries")]
    #[ORM\JoinColumn(name: "id_rec", referencedColumnName: "id_rec", onDelete: "CASCADE")]
    private ?Reclamation $id_rec = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $id_user = null;

    #[ORM\Column(type: "text", nullable: false)]
    #[Assert\NotBlank(message: "Le message ne peut pas être vide.")]
    private ?string $message = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $datemessage = null;

    #[ORM\Column(type: "string", columnDefinition: "ENUM('client', 'admin', '')")]
    private ?string $sender = null;

    #[ORM\Column(type: "string", columnDefinition: "ENUM('client', 'admin', '')")]
    private ?string $receiver = null;

    public function getIdM(): ?int
    {
        return $this->id_m;
    }

    public function getIdRec(): ?Reclamation

    {
        return $this->id_rec;
    }


    public function setIdRec(?Reclamation $id_rec): self
    {
        $this->id_rec = $id_rec;
        return $this;
    }

    public function getIdUser(): ?int

    {
        return $this->id_user;
    }


    public function setIdUser(?int $id_user): self
    {
        $this->id_user = $id_user;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getDatemessage(): ?\DateTimeInterface
    {
        return $this->datemessage;
    }

    public function setDatemessage(?\DateTimeInterface $datemessage): self
    {
        $this->datemessage = $datemessage;
        return $this;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(?string $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getReceiver(): ?string

    {
        return $this->receiver;
    }

    public function setReceiver(?string $receiver): self
    {
        $this->receiver = $receiver;
        return $this;
    }
}

