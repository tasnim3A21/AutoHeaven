<?php

namespace App\Event;

use App\Entity\Reclamation;
use Symfony\Contracts\EventDispatcher\Event;

class ReclamationSubmittedEvent extends Event
{
    public const NAME = 'reclamation.submitted';

    private Reclamation $reclamation;

    public function __construct(Reclamation $reclamation)
    {
        $this->reclamation = $reclamation;
    }

    public function getReclamation(): Reclamation
    {
        return $this->reclamation;
    }

    public function getUserData(): array
    {
        // Retourner les données statiques pour l'utilisateur avec ID 3
        if ($this->reclamation->getId() === 3) {
            return [
                'nom' => 'ahlem',
                'prenom' => 'ahlem',
                'tel' => '58732503',
                'email' => 'ahlemhidri724@gmail.com',
            ];
        }
        return [
            'nom' => 'N/A',
            'prenom' => 'N/A',
            'tel' => 'N/A',
            'email' => 'N/A',
        ];
    }

    public function isUrgent(): bool
    {
        $title = strtolower($this->reclamation->getTitre() ?? '');
        $content = strtolower($this->reclamation->getContenu() ?? '');
        return $this->reclamation->isUrgent() || str_contains($title, 'urgente') || str_contains($content, 'urgente') || str_contains($title, 'importante') || str_contains($content, 'importante');
    }
}