<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Lib\Instagram;


use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
//use Maalls\SocialMediaContentBundle\Lib\Twitter\Stream;
//use \Doctrine\ORM\EntityManagerInterface;
use Maalls\SocialMediaContentBundle\Lib\Instagram\Api;
use Maalls\SocialMediaContentBundle\Lib\Logger\Logger;

class ApiTest extends KernelTestCase
{
    public function testRequest()
    {

        $access_token = getenv("INSTAGRAM_ACCESS_TOKEN");

        if(!$access_token) {

            $this->assertTrue(true);
            return;

        }

        $api = new Api($access_token);
        //$api->setLogger(new Logger());
        $rsp = $api->request("tags/search", ["q" => "ultrasupernew"]);

        $rsp = json_decode($rsp);
        $tag = $rsp->data[0];
        $this->assertEquals("ultrasupernew", $tag->name);
        
        $rsp = $api->request("tags/cocacola/media/recent", ["min_tag_id" => null, "max_tag_id" => null]);

        var_dump($rsp);

        

    }

}
