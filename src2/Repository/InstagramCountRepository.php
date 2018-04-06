<?php

namespace Maalls\SocialMediaContentBundle\Repository;

use Maalls\SocialMediaContentBundle\Entity\InstagramCount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;


class InstagramCountRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InstagramCount::class);
    }


    public function countAllWithOffset()
    {

        return $this->createQueryBuilder("i")
            ->select("sum(i.count - i.offset_count)")
            ->getQuery()
            ->getSingleScalarResult();

    }

}
