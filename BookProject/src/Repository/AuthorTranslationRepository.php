<?php

namespace App\Repository;

use App\Entity\AuthorTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AuthorTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthorTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthorTranslation[]    findAll()
 * @method AuthorTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthorTranslation::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(AuthorTranslation $entity, bool $flush = true): void
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
    public function remove(AuthorTranslation $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
