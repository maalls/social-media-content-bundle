<?php 


namespace Maalls\SocialMediaContentBundle\Repository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class LoggableServiceEntityRepository extends ServiceEntityRepository {


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