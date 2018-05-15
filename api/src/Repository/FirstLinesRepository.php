<?php

namespace App\Repository;

use App\Entity\FirstLines;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FirstLines|null find($id, $lockMode = null, $lockVersion = null)
 * @method FirstLines|null findOneBy(array $criteria, array $orderBy = null)
 * @method FirstLines[]    findAll()
 * @method FirstLines[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FirstLinesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FirstLines::class);
    }

    public function findRandomFirstLine(): string
    {
      $conn = $this->getEntityManager()->getConnection();

      $sql = 'SELECT line FROM first_lines fl ORDER BY RAND() LIMIT 1';
      $stmt = $conn->prepare($sql);
      $stmt->execute();

      // returns an array of arrays (i.e. a raw data set)
      return $stmt->fetch()['line'];
    }

}
