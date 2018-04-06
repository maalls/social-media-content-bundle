<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Repository;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Maalls\SocialMediaContentBundle\Entity\Search;

class SearchRepositoryTest extends KernelTestCase
{

    public function testGetNextTwitterSearch()
    {
        $em = $this->getEntityManager();

        $this->truncateAll();
        $search = new Search();
        $search->setType("search_tweets");
        $search->setQuery("redbull");
        $search->setCreatedAt(new \Datetime());
        $search->setUpdatedAt(new \Datetime());
        $em->persist($search);
        $em->flush();
        $rsp = $em->getRepository(Search::class)->getNextTwitterSearch();
        $this->assertTrue($rsp === null);

        $search->setScheduledAt(new \Datetime());
        $em->persist($search);
        $em->flush();
        $rsp = $em->getRepository(Search::class)->getNextTwitterSearch();
        $this->assertEquals($search->getId(), $rsp->getId());


        $date = new \Datetime(date("Y-m-d H:i:s", time() + 3600));
        $search->setScheduledAt($date);
        $em->persist($search);
        $em->flush();
        $rsp = $em->getRepository(Search::class)->getNextTwitterSearch();
        $this->assertEquals(null, $rsp);


        $search = new Search();
        $search->setType("other_search");
        $search->setQuery("redbull");
        $search->setCreatedAt(new \Datetime());
        $search->setUpdatedAt(new \Datetime());
        $search->setScheduledAt(new \Datetime());
        $em->persist($search);
        $em->flush();

        $rsp = $em->getRepository(Search::class)->getNextTwitterSearch();
        $this->assertEquals(null, $rsp);


    }

}