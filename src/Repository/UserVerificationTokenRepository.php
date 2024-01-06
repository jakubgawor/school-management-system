<?php

namespace App\Repository;

use App\Entity\UserVerificationToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserVerificationToken>
 *
 * @method UserVerificationToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserVerificationToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserVerificationToken[]    findAll()
 * @method UserVerificationToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserVerificationTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserVerificationToken::class);
    }

    public function findOneByToken(string $token): ?UserVerificationToken
    {
        return $this->createQueryBuilder('t')
            ->where('t.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return UserVerificationToken[] Returns an array of UserVerificationToken objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserVerificationToken
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
