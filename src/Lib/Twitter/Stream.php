<?php 

namespace Maalls\SocialMediaContentBundle\Lib\Twitter;

use Maalls\SocialMediaContentBundle\Entity\Tweet;

class Stream extends \OauthPhirehose
{

    protected $log;

    protected $firebase;

    protected $counter;

    public function setEntityManager($em)
    {

    $this->em = $em;

    }

    public function setFirebase($firebase)
    {

        $this->firebase = $firebase;

    }

    public function setCounter($counter)
    {

        $this->counter = $counter;

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
        
            $status = json_decode($status);
            $tweet = $this->em->getRepository(Tweet::class)->generateFromJson($status, new \Datetime());
            $this->em->flush();
            $this->log("status: " . $tweet->getId());

            if($this->firebase) {

                $count = $this->em->getRepository(Tweet::class)
                    ->countAll();
                $this->log("$count tweets");
                $rsp = $this->firebase->set("/bazooka/twitter/", ["count" => $count, "status" => $status]);
                $rsp = $this->firebase->set("/bazooka/total/", ["count" => $this->counter->getCount()]);

                

            }

        }
        catch(\Exception $e) {

            //echo $status . PHP_EOL;

            throw $e;

        }

    }


    public function log($msg, $level = "info") 
    {

    if($this->log) $this->log->log($msg, $level);

    }
}