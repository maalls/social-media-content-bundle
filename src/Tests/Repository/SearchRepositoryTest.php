<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Repository;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
use \Doctrine\ORM\EntityManagerInterface;
use Maalls\SocialMediaContentBundle\Entity\Search;

class SearchRepositoryTest extends KernelTestCase
{

    public function testGetNextTwitterSearch()
    {
        $em = $this->getEntityManager();

        $this->truncateAll();

        // Schedule date needs to be set
        $search = new Search();
        $search->setType("search_tweets");
        $search->setQuery("redbull");
        $search->setCreatedAt(new \Datetime());
        $search->setUpdatedAt(new \Datetime());
        $em->persist($search);
        $em->flush();
        $rsp = $em->getRepository(Search::class)->getNextTwitterSearch();
        $this->assertTrue($rsp === null);


        // Setting schedule date and it returns the search.
        $search->setScheduledAt(new \Datetime());
        $em->persist($search);
        $em->flush();
        $rsp = $em->getRepository(Search::class)->getNextTwitterSearch();
        $this->assertEquals($search->getId(), $rsp->getId());


         // Having 2 search scheduled returns the earliest one.
        $search = new Search();
        $search->setType("search_tweets");
        $search->setQuery("redbull2");
        $search->setCreatedAt(new \Datetime());
        $search->setUpdatedAt(new \Datetime());
        $search->setScheduledAt(new \Datetime(date("Y-m-d H:i:s", time() - 3600)));
        $em->persist($search);
        $em->flush();
        $rsp = $em->getRepository(Search::class)->getNextTwitterSearch();
        $this->assertEquals("redbull2", $rsp->getQuery());
       
        $this->truncateAll();
       
        // Search scheduled for later is not returned.
        $date = new \Datetime(date("Y-m-d H:i:s", time() + 3600));
        $search->setScheduledAt($date);
        $em->persist($search);
        $em->flush();
        $rsp = $em->getRepository(Search::class)->getNextTwitterSearch();
        $this->assertEquals(null, $rsp);

        // Search of another type is not returned.
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