<?php

namespace Maalls\SocialMediaContentBundle\Repository;

use Maalls\SocialMediaContentBundle\Entity\TwitterTrend;
use Maalls\SocialMediaContentBundle\Repository\LoggableServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TwitterTrendRepository extends LoggableServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TwitterTrend::class);
    }

}