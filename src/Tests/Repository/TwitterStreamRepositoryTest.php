<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Repository;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Maalls\SocialMediaContentBundle\Entity\TwitterStream;


class TwitterStreamRepositoryTest extends KernelTestCase
{

    public function testGetTrack() 
    {

        $this->truncateAll();

        $stream = new TwitterStream();
        $stream->setTrack("hello,world");

        $this->em->persist($stream);

        $stream = new TwitterStream();
        $stream->setTrack("hello,dolly");

        $this->em->persist($stream);

        $this->em->flush();

        $track = $this->em->getRepository(TwitterStream::class)->getTrack();

        $this->assertEquals(["hello","world","dolly"], $track);

    }

}


