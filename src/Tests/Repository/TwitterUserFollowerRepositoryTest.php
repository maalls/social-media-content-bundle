<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Repository;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
use \Doctrine\ORM\EntityManagerInterface;
use Maalls\SocialMediaContentBundle\Entity\TwitterUserFollower;
use Maalls\SocialMediaContentBundle\Entity\TwitterUser;


class TwitterUserFollowerRepositoryTest extends KernelTestCase
{

    public function testGenerateFollowersFromTwitterUser() 
    {

        $this->truncateAll();

        $rep = $this->em->getRepository(TwitterUserFollower::class);
        $user = new TwitterUser();
        $user->setId("7812392");
        $user->setScreenName("ultrasupernew");
        $user->setUpdatedAt(new \Datetime());
        $user->setProfileUpdatedAt(new \Datetime());
        $this->em->persist($user);
        $this->em->flush();

        $rep->generateFollowersFromTwitterUser($user);
        
        $followersCount = $rep
            ->createQueryBuilder("f")
            ->select("count(f)")
            ->where("f.twitterUser = :user")
            ->setParameter("user", $user)
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertTrue($followersCount > 1000);


    }

    public function testGenerateFriendsFromTwitterUser() 
    {

        $this->truncateAll();

        $rep = $this->em->getRepository(TwitterUserFollower::class);
        $user = new TwitterUser();
        $user->setId("7812392");
        $user->setScreenName("ultrasupernew");
        $user->setUpdatedAt(new \Datetime());
        $user->setProfileUpdatedAt(new \Datetime());
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