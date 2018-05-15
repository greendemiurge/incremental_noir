<?php

namespace App\Repository;

use App\Entity\PromptWord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PromptWord|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromptWord|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromptWord[]    findAll()
 * @method PromptWord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromptWordRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, PromptWord::class);
  }

  public function findPromptWords(): array
  {
    $conn = $this->getEntityManager()->getConnection();

    $sql = 'SELECT word FROM prompt_word pw ORDER BY RAND() LIMIT 3';
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll();
    return array_map('current', $result);
  }

}
