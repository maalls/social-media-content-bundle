<?php

namespace Maalls\SocialMediaContentBundle\Lib\Logger;

class Logger {


    public function log($msg, $level = "info")
    {

        echo date("Y-m-d H:i:s") . " [$level] $msg" . PHP_EOL;

    }

}