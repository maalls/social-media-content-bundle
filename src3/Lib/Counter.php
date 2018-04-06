<?php 

namespace Maalls\SocialMediaContentBundle\Lib;
use Maalls\SocialMediaContentBundle\Entity\Tweet;
use Maalls\SocialMediaContentBundle\Entity\InstagramCount;

class Counter {

    public function __construct(\Doctrine\Common\Persistence\ObjectManager $em)
    {

        $this->em = $em;
        
    }

    public function getCount()
    {

        $twitterCount = $this->em->getRepository(Tweet::class)->countAll();
        $InstagramCount = $this->em->getRepository(InstagramCount::class)->countAllWithOffset();

        return $twitterCount + $InstagramCount;

    }

}