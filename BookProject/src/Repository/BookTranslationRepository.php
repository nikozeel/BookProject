<?php

namespace App\Repository;

use App\Entity\BookTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookTranslation[]    findAll()
 * @method BookTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookTranslation::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(BookTranslation $entity, bool $flush = true): void
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
    public function remove(BookTranslation $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
