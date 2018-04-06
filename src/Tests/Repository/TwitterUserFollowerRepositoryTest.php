<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Repository;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Maalls\SocialMediaContentBundle\Entity\TwitterUserFollower;
use Maalls\SocialMediaContentBundle\Entity\TwitterUser;


class TwitterUserFollowerRepositoryTest extends KernelTestCase
{

    public function testGenerateFromTwitterUser() 
    {

        $this->truncateAll();

        $rep = $this->em->getRepository(TwitterUserFollower::class);
        $user = new TwitterUser();
        $user->setId("115639376");
        $user->setScreenName("akiko_lawson");
        $user->setUpdatedAt(new \Datetime());
        $this->em->persist($user);
        $this->em->flush();

        $rep->generateFriendsFromTwitterUser($user);
        
        $friendCount = $rep
            ->createQueryBuilder("f")
            ->select("count(f)")
            ->where("f.follower = :user")
            ->setParameter("user", $user)
            ->getQuery()
            ->getSingleScalarResult();


        $this->assertTrue($friendCount > 1);


    }


}