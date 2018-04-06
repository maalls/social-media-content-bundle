<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Lib\Twitter;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
//use Maalls\SocialMediaContentBundle\Lib\Twitter\Stream;
//use Doctrine\Common\Persistence\ObjectManager;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Api;

class ApiTest extends KernelTestCase
{
    public function testGet()
    {

        $api = new Api(getenv("TWITTER_CREDENTIALS_FILE"), getenv("TWITTER_API_CACHE_FOLDER"));
        $api->setCacheLocation('');
        $rsp = $api->get("search/tweets", ["q" => "nike", "count" => 5]);
        $this->assertEquals(5, count($rsp->statuses));

    }

}