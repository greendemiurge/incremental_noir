<?php

namespace App\Repository;

use App\Entity\StoryThread;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StoryThread|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoryThread|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoryThread[]    findAll()
 * @method StoryThread[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoryThreadRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StoryThread::class);
    }

    public function findAvailableThreads() {
      $conn = $this->getEntityManager()->getConnection();

      $sql = 'SELECT id FROM story_thread st WHERE is_complete = 0 AND (lease_expiration IS NULL || lease_expiration = 0 || lease_expiration < NOW()) ORDER BY RAND() LIMIT 4';
      $stmt = $conn->prepare($sql);
      $stmt->execute();

      // returns an array of arrays (i.e. a raw data set)
      return $stmt->fetchAll();
    }

    public function findRandomCompleteId() {
      $conn = $this->getEntityManager()->getConnection();

      $sql = 'SELECT st.id FROM story_thread st JOIN story_line l ON st.id = l.story_thread_id GROUP BY st.id HAVING count(l.id) > 1 ORDER BY is_complete DESC, RAND() LIMIT 1';
//      $sql = 'SELECT id FROM story_thread st WHERE is_complete = 1 ORDER BY RAND() LIMIT 1 ';
      $stmt = $conn->prepare($sql);
      $stmt->execute();

      // returns an array of arrays (i.e. a raw data set)
      return (int) $stmt->fetch()['id'];
    }

}
