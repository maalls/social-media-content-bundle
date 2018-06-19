<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Repository;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
use \Doctrine\ORM\EntityManagerInterface;
use Maalls\SocialMediaContentBundle\Entity\TwitterUser;
use Maalls\SocialMediaContentBundle\Entity\Tweet;

class TwitterUserRepositoryTest extends KernelTestCase
{


    public function testUpdateProfiles()
    {

        $this->truncateAll();

        $rep = $this->em->getRepository(TwitterUser::class);
        
        $user = new TwitterUser();
        $user->setId(7812392); // USN
        $user->setUpdatedAt(new \Datetime());
        $user->setStatus(200);
        $this->em->persist($user);

        $user = new TwitterUser();
        $user->setId(1079156989); // LenzDev
        $user->setUpdatedAt(new \Datetime());
        $user->setStatus(200);
        $this->em->persist($user);

        $user = new TwitterUser();
        $user->setId(12343248734); // 404
        $user->setStatus(200);
        $user->setUpdatedAt(new \Datetime());
        $this->em->persist($user);

        $user = new TwitterUser();
        $user->setId(1234324873288884); // already updated
        $user->setUpdatedAt(new \Datetime());
        $user->setProfileUpdatedAt(new \Datetime());
        $this->em->persist($user);

        $this->em->flush();

        $count = $rep->updateProfiles();

        $this->assertEquals(3, $count);



    }

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
        $user->setId(783214);
        $user->setScreenName("twitter");
        $user->setUpdatedAt(new \Datetime());
        $user->setProfileUpdatedAt(new \Datetime());

        $this->em->persist($user);
        $this->em->flush();

        $apiMock = $this->createMock(\Maalls\SocialMediaContentBundle\Lib\Twitter\Api::class);

        $json = json_decode(file_get_contents(__dir__ . "/twitter_timeline_sample.json"));

        $apiMock->expects($this->any())
            ->method('get')
            ->willReturn($json);

        $apiMock->expects($this->any())
            ->method('getApiDatetime')
            ->willReturn(new \Datetime("1885-07-16 02:03:04"));

        $rep = $this->em->getRepository(TwitterUser::class, $apiMock);
        

        $result = $rep->updateTimeline($user);

        $this->assertEquals(478, $user->getRetweetMedian());
        $this->assertEquals(1794, $user->getFavoriteMedian());
        $this->assertEquals(0.465, $user->getRetweetRate());
        $this->assertEquals(8193, $user->getPostPeriodMedian());
        $this->assertEquals("1885-07-16 02:03:04", $user->getTimelineUpdatedAt()->format("Y-m-d H:i:s"));
        
        $count = $this->em->getRepository(Tweet::class, $apiMock)
            ->createQueryBuilder("t")->select("count(t)")
            ->where("t.user = :user")->setParameter("user", $user)
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(200, $count);


    }

    public function testUpdateAllTimelines()
    {

        $this->truncateAll();
        $now = '2018-01-01 12:12:12';
        $stmt = $this->em->getConnection()->prepare("
            insert into 
                twitter_user (status, lang, protected, followers_count, id, screen_name, updated_at) 
            values 
                (200, 'ja', 0, 11000, 783214, 'twitter', '$now'), 
                (200, 'ja', 0, 13000, 7812392, 'ultrasupernew', '$now'),
                (200, 'ja', 0, 89, 42234234, 'dummy_account', '$now')
        ");

        $stmt->execute();


        $rep = $this->em->getRepository(TwitterUser::class);
        $done = $rep->updateAllTimelines();

        $this->assertEquals(2, $done);



    }

}