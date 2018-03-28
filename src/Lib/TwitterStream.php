<?php 

namespace Maalls\SocialMediaContentBundle\Lib;

use Maalls\SocialMediaContentBundle\Entity\Tweet;

class TwitterStream extends \OauthPhirehose
{

    protected $log;


    public function setEntityManager($em)
    {

    $this->em = $em;

    }


    public function setLog($log)
    {

    $this->log = $log;

    }
    /**
    * Enqueue each status
    *
    * @param string $status
    */
    public function enqueueStatus($status)
    {
    
        try {
        
            $tweet = $this->em->getRepository(Tweet::class)->generateFromJson(json_decode($status));
            $this->em->flush();
            $this->log("status: " . $tweet->getId());

        }
        catch(\Exception $e) {

            echo $status . PHP_EOL;

            throw $e;

        }

    }


    public function log($msg, $level = "info") 
    {

    if($this->log) $this->log->log($msg, $level);

    }
}