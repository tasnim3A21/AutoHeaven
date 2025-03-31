<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;

#[ORM\Entity]
class Messagerie
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_m;

        #[ORM\ManyToOne(targetEntity: Reclamation::class, inversedBy: "messageries")]
    #[ORM\JoinColumn(name: 'id_rec', referencedColumnName: 'id_rec', onDelete: 'CASCADE')]
    private Reclamation $id_rec;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "messageries")]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private User $id_user;

    #[ORM\Column(type: "string")]
    private string $sender;

    #[ORM\Column(type: "text")]
    private string $message;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $datemessage;

    #[ORM\Column(type: "string")]
    private string $receiver;

    public function getId_m()
    {
        return $this->id_m;
    }

    public function setId_m($value)
    {
        $this->id_m = $value;
    }

    public function getId_rec()
    {
        return $this->id_rec;
    }

    public function setId_rec($value)
    {
        $this->id_rec = $value;
    }

    public function getId_user()
    {
        return $this->id_user;
    }

    public function setId_user($value)
    {
        $this->id_user = $value;
    }

    public function getSender()
    {
        return $this->sender;
    }

    public function setSender($value)
    {
        $this->sender = $value;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($value)
    {
        $this->message = $value;
    }

    public function getDatemessage()
    {
        return $this->datemessage;
    }

    public function setDatemessage($value)
    {
        $this->datemessage = $value;
    }

    public function getReceiver()
    {
        return $this->receiver;
    }

    public function setReceiver($value)
    {
        $this->receiver = $value;
    }
}
