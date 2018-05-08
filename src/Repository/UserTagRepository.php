<?php

namespace Maalls\SocialMediaContentBundle\Repository;

use Maalls\SocialMediaContentBundle\Entity\UserTag;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Maalls\SocialMediaContentBundle\Repository\LoggableServiceEntityRepository;

class UserTagRepository extends LoggableServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {

        parent::__construct($registry, UserTag::class);
    }

}

