<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Lib\Twitter;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
//use Maalls\SocialMediaContentBundle\Lib\Twitter\Stream;
//use \Doctrine\ORM\EntityManagerInterface;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Search as TwitterSearch;
use Maalls\SocialMediaContentBundle\Entity\Search;
class SearchTest extends KernelTestCase
{
    public function testIterate()
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

        $apiMock = $this->createMock(\Maalls\SocialMediaContentBundle\Lib\Twitter\Api::class);

        $counterMock = $this->createMock(\Maalls\SocialMediaContentBundle\Service\Firebase\FirebaseCounter::class);

        $twitterSearch = new TwitterSearch($em, $apiMock, $counterMock);
        
        try {
            $twitterSearch->iterate($search); 
            $this->fail();
        }
        catch(\Exception $e) {

            $this->assertEquals(1010, $e->getCode());

        }

        $apiMock->method('get')
             ->willReturn(json_decode(json_encode(['errors' => [['message' => 'error message', 'code' => 666]]])));


        try {
            $twitterSearch->iterate($search); 
            $this->fail();
        }
        catch(\Exception $e) {

            $this->assertEquals(666, $e->getCode());
            $this->assertEquals("error message", $e->getMessage());

        }

        $json = json_decode(file_get_contents(__dir__ . "/search_tweets-sample.json"));

        count($json->statuses);

        $apiMock = $this->createMock(\Maalls\SocialMediaContentBundle\Lib\Twitter\Api::class);
        $apiMock->method('get')->willReturn($json);
        $apiMock->method('getApiDatetime')->willReturn(new \Datetime());
        $twitterSearch = new TwitterSearch($em, $apiMock, $counterMock);

        $count = $twitterSearch->iterate($search);

        $this->assertEquals(count($json->statuses), $count);



    }

}
