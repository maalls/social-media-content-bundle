<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Lib\Twitter;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Stream;
use \Doctrine\ORM\EntityManagerInterface;
use Maalls\SocialMediaContentBundle\Entity\Tweet;
class StreamTest extends KernelTestCase
{

    

    

    public function testEnqueueStatus()
    {

        $this->truncateAll();
        $stream = new Stream('a', 'b');
        $em = $this->em;
        $stream->setEntityManager($em);
        $fh = fopen(__dir__ . "/tracking-sample.json", "r");
        $json = fgets($fh);
        $stream->enqueueStatus($json);

        $status = json_decode($json);

        $tweet = $em->getRepository(Tweet::class)->find($status->id_str);


        $this->assertTrue($tweet != null);
        $this->assertEquals($status->id_str, $tweet->getId());
        

    }

}