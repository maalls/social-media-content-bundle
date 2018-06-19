<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Repository;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
use \Doctrine\ORM\EntityManagerInterface;
use Maalls\SocialMediaContentBundle\Entity\Tweet;
use Maalls\SocialMediaContentBundle\Entity\TwitterUser;

class TwitterStreamTest extends KernelTestCase
{

    

    public function testGenerateFromJson()
    {

        $this->truncateAll();
        
        

        $fh = fopen(__dir__ . "/tweet_sample.json", "r");
        $json = json_decode(fgets($fh));

        $em = $this->getEntityManager();
        $rep = $em->getRepository(Tweet::class);
        $dataDatetime = new \Datetime("2018-01-02 06:05:04");
        $tweet = $rep->generateFromJson($json, $dataDatetime);

        $em->flush();

        $this->assertEquals(Tweet::class, get_class($tweet));

        $tweet = $em->getRepository(Tweet::class)->find($json->id_str);

        $this->assertEquals($json->id_str, $tweet->getId());

        $this->assertEquals($dataDatetime->format("Y-m-d H:i:s"), $tweet->getStatsUpdatedAt()->format("Y-m-d H:i:s"));

    }

    public function testGenerateFromStatusId()
    {

        $expected_id = 908233161561088001;
        $status = $this->getRepository()->generateFromStatusId($expected_id);

        $this->assertEquals($expected_id, $status->getId());
        $this->assertEquals("2017-09-14 07:37:14", $status->getPostedAt()->format("Y-m-d H:i:s"));

        $tweet = $this->getRepository()->find($expected_id);
        $this->assertEquals($expected_id, $tweet->getId());

    }

    public function testUpdateRetweets()
    {

        $em = $this->getEntityManager();
        $rep = $em->getRepository(Tweet::class);

        $tweet = new Tweet();
        $tweet->setId(908233161561088001);
        $tweet->setPostedAt(new \Datetime('2016-10-03 06:28:00'));
        $tweet->setStatsUpdatedAt(new \Datetime());
        $em->persist($tweet);
        $em->flush();

        $count = $rep->updateRetweets($tweet, true);

        $this->assertGreaterThanOrEqual(1, $count);

        $retweets = $rep->findBy(["retweet_status" => $tweet->getId()]);

        $this->assertEquals($count, count($retweets));
    }

    protected function getRepository()
    {

        return $this->em->getRepository(Tweet::class);

    }

}