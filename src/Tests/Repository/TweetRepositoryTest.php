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
        $tweet = $rep->generateFromJson($json);

        $em->flush();

        $this->assertEquals(Tweet::class, get_class($tweet));

        $tweet = $em->getRepository(Tweet::class)->find($json->id_str);

        $this->assertEquals($json->id_str, $tweet->getId());

    }

}