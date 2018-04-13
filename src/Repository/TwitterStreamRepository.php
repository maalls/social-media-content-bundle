<?php

namespace Maalls\SocialMediaContentBundle\Repository;

use Maalls\SocialMediaContentBundle\Entity\TwitterStream;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TwitterStreamRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TwitterStream::class);
    }


    public function getTrack()
    {

        $streams = $this->findAll();
        $tracks = [];

        foreach($streams as $stream)
        {

            foreach(explode(",", $stream->getTrack()) as $keyword) {

                $keyword = trim($keyword);
                if(!in_array($keyword, $tracks)) {

                    $tracks[] = $keyword;

                }

            }

        }

        return $tracks;

    }

}