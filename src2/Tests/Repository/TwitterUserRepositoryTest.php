<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Repository;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Maalls\SocialMediaContentBundle\Entity\TwitterUser;

class TwitterUserRepositoryTest extends KernelTestCase
{

    public function testGenerateFromScreenName()
    {

        $this->truncateAll();

        $rep = $this->em->getRepository(TwitterUser::class);

        $user = $rep->generateFromScreenName("LenzDev");

        $this->assertEquals("LenzDev", $user->getScreenName());

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

        $rep->generateFromJson($profile);

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
        $user = $rep->hydrateJson($user, $profile);

        $this->assertEquals($profile->id_str, $user->getId());

        $this->assertEquals("Resseta", $user->getName());

        $this->assertEquals("Do what you love, surround yourself with the right people, snapchat : resseta IG: resseta don't follow if you're Homophobic or not open minded please", $user->getDescription());



    }

}