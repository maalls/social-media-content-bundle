<?php

namespace Maalls\SocialMediaContentBundle\Repository;

use Maalls\SocialMediaContentBundle\Entity\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Tweet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tweet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tweet[]    findAll()
 * @method Tweet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Search::class);
    }


    public function getNextTwitterSearch()
    {

        return $this->createQueryBuilder("ts")
                        ->where("ts.scheduled_at <= :now")->setParameter("now", date("Y-m-d H:i:s"))
                        ->andWhere("ts.query is not null or ts.query != ''")
                        ->andWhere("ts.type = 'search_tweets'")
                        ->orderBy("ts.scheduled_at", "ASC")
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();

    }

}
