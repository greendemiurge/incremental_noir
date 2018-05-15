<?php

namespace App\Repository;

use App\Entity\ArcSegment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ArcSegment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArcSegment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArcSegment[]    findAll()
 * @method ArcSegment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArcSegmentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ArcSegment::class);
    }

}
