<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Repository;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Maalls\SocialMediaContentBundle\Entity\InstagramCount;

class InstagramCountRepositoryTest extends KernelTestCase
{

    public function testCountAllWithOffset()
    {

        $this->truncateAll();

        $em = $this->getEntityManager();
        $instagramCount = new InstagramCount();
        $instagramCount->setCount(20);
        $instagramCount->setTag("hello");
        $instagramCount->setOffsetCount(10);

        $em->persist($instagramCount);

        $em = $this->getEntityManager();
        $instagramCount = new InstagramCount();
        $instagramCount->setCount(120);
        $instagramCount->setTag("hello again");
        $instagramCount->setOffsetCount(50);

        $em->persist($instagramCount);

        $em->flush();

        $count = $em->getRepository(InstagramCount::class)
            ->countAllWithOffset();


        $this->assertEquals(80, $count);

    }

}