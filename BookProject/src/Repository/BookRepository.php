<?php

namespace App\Repository;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\BookTranslation;
use App\Service\MapResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Book $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Book $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findAllAuthorBooksByOneBookName($_locale, $_search_query)
    {
        $expr = $this->_em->getExpressionBuilder();

        if ($_locale === "en") {
            $getBookAuthorSubQuery = $this->createQueryBuilder('b')
                ->select('a.id')
                ->distinct()
                ->join(Author::class, 'a', 'WITH', 'a.id = b.author')
                ->andWhere('b.name = :search_query')
                ->getQuery()
                ->getDQL();
        } else {
            $qb = $this->_em->createQueryBuilder();
            $getBookAuthorSubQuery = $qb
                ->select('a.id')
                ->distinct()
                ->from(BookTranslation::class, 'bt')
                ->join(Book::class, 'b', 'WITH', 'b.id = bt.book')
                ->join(Author::class, 'a', 'WITH', 'a.id = b.author')
                ->andWhere('bt.context = :search_query')
                ->andWhere('bt.locale = :locale')
                ->getQuery()
                ->getDQL();
        }

        $masterQuery = $this->createQueryBuilder('m')
            ->andWhere($expr->in('m.author', $getBookAuthorSubQuery))
            ->setParameter('search_query', $_search_query);
        ($_locale !== "en") ? $masterQuery->setParameter('locale', $_locale) : null;

        $queryResult = $masterQuery->getQuery()->getResult();
        
        if (empty($queryResult)) {
            throw new NotFoundHttpException();
        }

        return MapResponse::mapBookSearchResponse($_locale, $queryResult);
    }

    public function getBookById($_locale, $_id)
    {
        $bookItem = $this->find($_id);

        if ($bookItem) {
            return MapResponse::mapGetBookResponse($_locale, $bookItem);
        }

        throw new NotFoundHttpException();
    }
}
