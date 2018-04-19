<?php 

namespace Maalls\SocialMediaContentBundle\Lib\Twitter;

use Maalls\SocialMediaContentBundle\Entity\Tweet;
use Maalls\SocialMediaContentBundle\Entity\Search as EntitySearch;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Api;

class Search {

    protected $em;

    protected $logger;
    protected $counter;

    protected $period = 5;

    public function __construct(\Doctrine\ORM\EntityManagerInterface $em, Api $api, \Maalls\SocialMediaContentBundle\Service\Firebase\FirebaseCounter $counter) {

        $this->em = $em;
        $this->api = $api;
        $this->counter = $counter;

    }

    public function start()
    {
        
        $max_retry = 10;
        $retry = 0;

        do {

            try {
                
                $this->em->clear(EntitySearch::class);
                $search = $this->em->getRepository(EntitySearch::class)->getNextTwitterSearch();

                if($search) {

                    $this->log("Searching search ID " . $search->getId() . " from " . $search->getMaxId() . " down to " . $search->getSinceId());
                    $total = $this->paginate($search);
                    $this->log("Search done, $total results collected.");
                    if($total) {

                        $this->counter->update();

                    }
                    

                }
                else {

                    $this->log("No searches scheduled.");
                    sleep(10);


                }
                
                $retry = 0;


            }
            catch(Exception\Reschedule $e) {

                $search->setScheduledAt($e->getScheduledAt());
                $this->em->persists($search);
                $this->em->flush();

            }
            catch(\Exception $e) {


                $this->log("$retry / $max_retry Search error: " . $e->getMessage() . "(" . $e->getCode() . ")", "error");
                $retry++;
                sleep(10);
                
            }

        }
        while($retry < $max_retry);  

        throw $e;

    }

    public function paginate($search) 
    {

        $total = 0;
        do {

            $count = $this->iterate($search);
            $total += $count;

        }
        while($count);

        return $total;

    }

    public function iterate($search)
    {

        $params = $this->initializeParameters($search);
        $this->log("Calling search/tweets " . http_build_query($params));
        $rsp = $this->api->get("search/tweets", $params);
        $this->parseError($rsp);

        $searchDatetime = $this->api->getApiDatetime();
        $statuses = $rsp->statuses;
        $count = count($statuses);

        if($count) {

            $firstTweet = $statuses[0];

            if($firstTweet->id_str > $search->getGreatestId()) {

                $search->setGreatestId($firstTweet->id_str + 1);

            }

            $search->setMaxId($statuses[count($statuses) - 1]->id_str - 1);    

        }

        $this->em->getRepository(Tweet::class)->generateFromJsons($statuses, $searchDatetime);
        
        $search->setUpdatedAt(new \Datetime());

        if($count == 0) {

            $search->setSinceId($search->getGreatestId());
            $search->setMaxId(null);
            $search->setScheduledAt(new \Datetime(date("Y-m-d H:i:s", time() + $this->period)));
            
        }

        $search->setUpdatedAt(new \Datetime());
        $this->em->persist($search);
        $this->em->flush();

        return $count;



    }

    public function initializeParameters($search)
    {

        $params = ["q" => $search->getQuery(), "result_type" => "recent", "count" => 100];

        if($search->getSinceId()) {

            $params["since_id"] = $search->getSinceId() - 1;

        }

        if($search->getMaxId()) {
    
            $params["max_id"] = $search->getMaxId();

        }

        return $params;


    }

    public function parseError($response)
    {

        if(isset($response->errors)) {

            $error = $response->errors[0];

            if($error->code == 88) {

                throw new Exception\Reschedule($error->message, $error->code, new \Datetime(date("Y-m-d Hi:s", time() + 60)));

            }

            throw new \Exception($error->message, $error->code);
            
        }

        if(!isset($response->statuses)) {

            throw new \Exception("Unexpected response: " . json_encode($response), 1010);

        }

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