<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Lib\Instagram;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
//use Maalls\SocialMediaContentBundle\Lib\Twitter\Stream;
//use \Doctrine\ORM\EntityManagerInterface;
use Maalls\SocialMediaContentBundle\Lib\Instagram\Search as Search;


class SearchTest extends KernelTestCase
{
    public function testSearch()
    {

        $apiKey = getenv("INSTAGRAM_API_KEY");
        $apiSecret = getenv("INSTAGRAM_API_SECRET");
        $apiCallback = getenv("INSTAGRAM_API_CALLBACK");
        $accessToken = getenv('INSTAGRAM_ACESSS_TOKEN');

        if(!$apiKey || !$apiSecret || !$apiCallback || !$accessToken) {

            $this->assertTrue(true);
            return;

        }

        $config = [
            'apiKey' => $apiKey, 
            'apiSecret' => $apiSecret, 
            'apiCallback' => $apiCallback
        ];

        $api = new \MetzWeb\Instagram\Instagram($config);

        $api->setAccessToken();

        $rsp = $api->getTag("redbull");

        var_dump($rsp);

        $em = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);

        $search = new Search($em, $api);

        

    }

}
