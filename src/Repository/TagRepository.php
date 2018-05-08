<?php

namespace Maalls\SocialMediaContentBundle\Repository;

use Maalls\SocialMediaContentBundle\Entity\Tag;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Maalls\SocialMediaContentBundle\Repository\LoggableServiceEntityRepository;

class TagRepository extends LoggableServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {

        parent::__construct($registry, Tag::class);
    }

}

