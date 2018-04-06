<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Lib\Firebase;

use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
use Maalls\SocialMediaContentBundle\Lib\Firebase\Firebase as Firebase;

class FirebaseTest extends KernelTestCase
{


    public function testSet()
    {


        $url = getenv("FIREBASE_DATABASE_URL");
        $key = getenv("FIREBASE_API_KEY");

        if(!$url || !$key) {

            $this->assertTrue(true);
            return;   

        }

        $firebase = new Firebase($url, $key);

        $time = time();
        $rsp = $firebase->set("test/count", $time);

        var_dump($rsp);



    }

}