<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }
    public function search(string $searchTerm): array
{
    return $this->createQueryBuilder('c')
        ->join('c.id', 'u')
        ->addSelect('u')
        ->where('u.nom LIKE :search OR u.prenom LIKE :search OR u.tel LIKE :search OR c.id_com LIKE :search')
        ->setParameter('search', '%'.$searchTerm.'%')
        ->orderBy('c.date_com', 'DESC')
        ->getQuery()
        ->getResult();
}
public function aff(string $searchTerm): array {
return $this->createQueryBuilder('c')
->select('c', 'u', 'lc', 'e')
->join('c.id', 'u') 
->leftJoin('c.lignecommandes', 'lc') 
->leftJoin('lc.id_e', 'e') 
->where('c.id_com = :id')
->setParameter('id', $id)
->getQuery()
->getOneOrNullResult();

   }   // Add custom methods as needed
}