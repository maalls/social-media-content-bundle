<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Repository;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Maalls\SocialMediaContentBundle\Entity\TwitterUser;
use Maalls\SocialMediaContentBundle\Entity\Tweet;

class TwitterUserRepositoryTest extends KernelTestCase
{

    public function testGenerateFromScreenName()
    {

        $this->truncateAll();

        $rep = $this->em->getRepository(TwitterUser::class);

        $user = $rep->generateFromScreenName("LenzDev");

        $this->assertEquals("LenzDev", $user->getScreenName());

        $user = $rep->generateFromScreenName("hdsafhj778787sdfdsfsdf");

        $this->assertEquals(false, $user);

        



    }

    public function testGenerateFromJson() 
    {

        $this->truncateAll();

        $rep = $this->em->getRepository(TwitterUser::class);

        $fh = fopen(__dir__ . "/tweet_sample.json", "r");
        $json = fgets($fh);

        $status = json_decode($json);
        $profile = $status->user;

        $rep = $this->em->getRepository(TwitterUser::class);

        $rep->generateFromJson($profile, new \Datetime());

        $this->em->flush();

        $user = $this->em->getRepository(TwitterUser::class)->find($profile->id_str);

        $this->assertTrue($user != null);


    }

    public function testHydrateJson()
    {


        $fh = fopen(__dir__ . "/tweet_sample.json", "r");
        $json = fgets($fh);

        $status = json_decode($json);

        $profile = $status->user;

        $em = $this->getEntityManager();
        $rep = $em->getRepository(TwitterUser::class);

        $user = new TwitterUser();
        $now = new \Datetime('2018-01-02 03:04:05');
        $user = $rep->hydrateJson($user, $profile, $now);

        $this->assertEquals($profile->id_str, $user->getId());

        $this->assertEquals("Resseta", $user->getName());
        $this->assertEquals('2018-01-02 03:04:05', $user->getProfileUpdatedAt()->format("Y-m-d H:i:s"));

        $this->assertEquals("Do what you love, surround yourself with the right people, snapchat : resseta IG: resseta don't follow if you're Homophobic or not open minded please", $user->getDescription());



    }

    public function testUpdateTimeline()
    {

        $this->truncateAll();

        $user = new TwitterUser();
        $user->setId(7812392);
        $user->setScreenName("ultrasupernew");
        $user->setUpdatedAt(new \Datetime());
        $user->setProfileUpdatedAt(new \Datetime());

        $this->em->persist($user);
        $this->em->flush();

        $this->em->getRepository(TwitterUser::class)->updateTimeline($user);

        $count = $this->em->getRepository(Tweet::class)
            ->createQueryBuilder("t")->select("count(t)")
            ->where("t.user = :user")->setParameter("user", $user)
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(200, $count);


    }

}