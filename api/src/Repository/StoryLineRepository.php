<?php

namespace App\Repository;

use App\Entity\StoryLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StoryLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoryLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoryLine[]    findAll()
 * @method StoryLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoryLineRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StoryLine::class);
    }

}
