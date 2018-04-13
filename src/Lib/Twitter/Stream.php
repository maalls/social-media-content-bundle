<?php 

namespace Maalls\SocialMediaContentBundle\Lib\Twitter;

use Maalls\SocialMediaContentBundle\Entity\Tweet;
use Maalls\SocialMediaContentBundle\Entity\TwitterStream;

class Stream extends \OauthPhirehose
{

    protected $log;

    protected $firebase;

    protected $counter;

    
    public function consume($reconnect = true)
    {

        $this->checkFilterPredicates();

        return parent::consume($reconnect);

    }


    protected function checkFilterPredicates()
      {
        
        $this->em->clear(TwitterStream::class);
        $track = $this->em->getRepository(TwitterStream::class)->getTrack();

        $current_track = $this->getTrack();

        if($current_track != $track || !$this->getTrack()) {

            if($track) {

                $this->log("Track set to " . implode(",", $track));
                $this->setTrack($track);

            }
            else {

                $this->log("Nothing to track, track something impossible");
                $this->setTrack(["hhfjh7788sdfhhY8899923"]);

            }

        }

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
            $this->em->clear();
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


    public function log($msg, $level = "info") 
    {

    if($this->log) $this->log->log($msg, $level);

    }
}