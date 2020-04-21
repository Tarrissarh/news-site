<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Get all posts for pagination
     *
     * @return Query
     */
    public function findAllPaginationQuery(): Query
    {
        return $this->createQueryBuilder('p')->addOrderBy('p.pubDate', 'desc')->getQuery();
    }

    /**
     * Get all posts query for pagination
     *
     * @param string $category
     *
     * @return Query
     */
    public function findByCategoryPagination(string $category): Query
    {
        return $this->createQueryBuilder('p')
                    ->where('p.category LIKE :category')
                    ->setParameter('category', '%' . $category . '%')
                    ->getQuery();
    }

    /**
     * Get all posts who have activity (daily, week, month, year)
     *
     * @return mixed
     */
    public function getActivityPosts()
    {
        $query = $this->createQueryBuilder('p')
                      ->where('p.isDaily = :active')
                      ->orWhere('p.isWeek = :active')
                      ->orWhere('p.isMonth = :active')
                      ->orWhere('p.isYear = :active')
                      ->setParameter('active', 1)
                      ->getQuery();

        return $query->getResult();
    }

    /**
     * Get all link posts
     *
     * @return mixed
     */
    public function findAllLinks()
    {
        return $this->createQueryBuilder('p')
                    ->select('p.link')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * Get all posts as array
     *
     * @return mixed
     */
    public function findAllInArray()
    {
        return $this->createQueryBuilder('p')
                    ->getQuery()
                    ->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * Reset all daily posts
     *
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetPosts(): bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql  = '
				UPDATE post p 
				SET p.is_daily = :is_action, 
				    p.is_week = :is_action, 
				    p.is_month = :is_action, 
				    p.is_year = :is_action
			   ';
        $stmt = $conn->prepare($sql);

        return $stmt->execute(['is_action' => 0]);
    }

    /**
     * Search
     *
     * @param string $query
     *
     * @return Query
     */
    public function search(string $query): Query
    {
        $searchQuery = $this->createQueryBuilder('p')
                            ->where('p.title LIKE :query')
                            ->orWhere('p.description LIKE :query')
                            ->orWhere('p.category LIKE :query')
                            ->setParameter('query', '%' . $query . '%')
                            ->getQuery();

        return $searchQuery;
    }

    /**
     * Search with category
     *
     * @param string $query
     * @param string $category
     *
     * @return Query
     */
    public function searchWithCategory(string $query, string $category): Query
    {
        $searchQuery = $this->createQueryBuilder('p')
                            ->where('p.title LIKE :query')
                            ->orWhere('p.description LIKE :query')
                            ->orWhere('p.category LIKE :query')
                            ->setParameter('query', '%' . $query . '%')
                            ->andWhere('p.category LIKE :category')
                            ->setParameter('category', '%' . $category . '%')
                            ->getQuery();

        return $searchQuery;
    }
}
