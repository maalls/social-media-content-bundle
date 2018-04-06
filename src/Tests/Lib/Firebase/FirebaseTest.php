<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests\Lib\Firebase;

use Maalls\SocialMediaContentBundle\Tests\KernelTestCase;
use Maalls\SocialMediaContentBundle\Lib\Firebase\Firebase as Firebase;

class FirebaseTest extends KernelTestCase
{


    public function testSet()
    {

        $firebase = new Firebase("https://bazooka-bb1b1.firebaseio.com", "AIzaSyDlArJi0Q7uMM8PueqjS9ukP4Gy6uFlZ4c");

        $time = time();
        $rsp = $firebase->set("test/count", $time);

        var_dump($rsp);



    }

}