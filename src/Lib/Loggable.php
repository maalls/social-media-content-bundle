<?php

namespace Maalls\SocialMediaContentBundle\Lib;

class Loggable {

    protected $logger;

    public function setLogger($logger)
    {

        $this->logger = $logger;

    }

    public function log($msg, $level = "info")
    {

        if($this->logger) {

            $this->logger->log($msg, $level);

        }

    }

}