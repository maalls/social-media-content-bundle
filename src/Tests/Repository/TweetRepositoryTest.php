<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Repository;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
use Doctrine\Common\Persistence\ObjectManager;
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

}