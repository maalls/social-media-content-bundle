<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Lib\Instagram;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
//use Maalls\SocialMediaContentBundle\Lib\TwitterStream;
//use Doctrine\Common\Persistence\ObjectManager;
use Maalls\SocialMediaContentBundle\Lib\Instagram\Search as Search;


class SearchTest extends KernelTestCase
{
    public function testSearch()
    {

        $api = new \MetzWeb\Instagram\Instagram([
            'apiKey' => '274c646c0561492b9b49e886391c99a3', 
            'apiSecret' => 'fe64a12aeaab4fa9b12509b3e4150dda', 
            'apiCallback' => 'http://hashtag-counter.an.dev.jp/instagram/redirect'
        ]);

        $api->setAccessToken("177559794.274c646.596c8384741c4188a9e2dcf396a8bddb");

        $rsp = $api->getTag("redbull");

        var_dump($rsp);

        $em = $this->createMock(\Doctrine\Common\Persistence\ObjectManager::class);

        $search = new Search($em, $api);

        

    }

}
