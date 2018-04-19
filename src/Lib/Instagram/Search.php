<?php 

namespace Maalls\SocialMediaContentBundle\Lib\Instagram;


class Search {

    protected $em;

    protected $logger;

    protected $period = 5;

    public function __construct(\Doctrine\ORM\EntityManagerInterface $em, \MetzWeb\Instagram\Instagram $api) {

        $this->em = $em;
        $this->api = $api;

    }

    

    public function setLogger($logger) {

        $this->logger = $logger;

    }

    public function log($msg, $level = "info")
    {

        if($this->logger) {

            $this->logger->log($msg, $level);

        }

    }
}