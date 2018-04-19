<?php

namespace Maalls\SocialMediaContentBundle\Service\Firebase;
use Maalls\SocialMediaContentBundle\Entity\Tweet;
use Maalls\SocialMediaContentBundle\Entity\InstagramCount;

class FirebaseCounter {

    private $firebase;
    private $em;

    public function __construct(\Maalls\SocialMediaContentBundle\Lib\Firebase\Firebase $firebase, \Doctrine\Common\Persistence\ObjectManager $em)
    {

        $this->firebase = $firebase;
        $this->em = $em;

    }

    public function update() 
    {

        $statuses = $this->em->getRepository(Tweet::class)->createQueryBuilder("t")
            ->orderBy("t.id", "desc")
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();

        if($statuses) {

            $status = $statuses[0];
            $data = ["user" => ["screen_name" => $status->getUser()->getScreenName()]];

        }
        else {

            $data = null;

        }
        
        $twitterCount = $this->em->getRepository(Tweet::class)->countAll();
        $rsp = $this->firebase->set("/bazooka/twitter/", ["count" => $twitterCount, "status" => $data]);

        $instagramCount = $this->em->getRepository(InstagramCount::class)->countAllWithOffset();
            
        $rsp = $this->firebase->set("/bazooka/total/", ["count" => $twitterCount + $instagramCount]);
        
        

    }

}