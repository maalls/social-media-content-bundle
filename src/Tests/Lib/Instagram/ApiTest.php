<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Lib\Instagram;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
//use Maalls\SocialMediaContentBundle\Lib\TwitterStream;
//use Doctrine\Common\Persistence\ObjectManager;
use Maalls\SocialMediaContentBundle\Lib\Instagram\Api;
use Maalls\SocialMediaContentBundle\Lib\Logger\Logger;

class ApiTest extends KernelTestCase
{
    public function testRequest()
    {

        $api = new Api("177559794.274c646.596c8384741c4188a9e2dcf396a8bddb");
        $api->setLogger(new Logger());
        $rsp = $api->request("tags/search", ["q" => "ultrasupernew"]);

        $rsp = json_decode($rsp);
        $tag = $rsp->data[0];
        $this->assertEquals("ultrasupernew", $tag->name);
        
        $rsp = $api->request("tags/cocacola/media/recent", ["min_tag_id" => null, "max_tag_id" => null]);

        var_dump($rsp);

        

    }

}
