<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Avis;

#[ORM\Entity]
class Mention_j_aime
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_mention;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "mention_j_aimes")]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private User $id_user;

        #[ORM\ManyToOne(targetEntity: Avis::class, inversedBy: "mention_j_aimes")]
    #[ORM\JoinColumn(name: 'id_a', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Avis $id_a;

    public function getId_mention()
    {
        return $this->id_mention;
    }

    public function setId_mention($value)
    {
        $this->id_mention = $value;
    }

    public function getId_user()
    {
        return $this->id_user;
    }

    public function setId_user($value)
    {
        $this->id_user = $value;
    }

    public function getId_a()
    {
        return $this->id_a;
    }

    public function setId_a($value)
    {
        $this->id_a = $value;
    }
}
