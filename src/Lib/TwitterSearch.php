<?php 

namespace Maalls\SocialMediaContentBundle\Lib;

class TwitterSearch {

    protected $api;

    protected $pool;

    protected $db;

    public function __construct(\Doctrine\Common\Persistence\ObjectManager $em, Abraham\TwitterOAuth\TwitterOAuth $api) {

        $this->em = $em;
        $this->api = $api;

    }



    // This will collect all the tweet between since_id and max_id.
    // If max_id not define, it will collect from the lasted.
    // If since_id is not defined, it will collect until no search results are returned.
    // It also returns the new max_id and since_id.
    // If there is no issue, max_id will be null and since_id set to the lastest.
    // In case of issue (connection failure) max_id will be set to oldest tweet retrieved and since_id the same as passed as parameter.
    // In this case iterate should be called again later including these parameters.

    public function run()
    {
    
        $retry = 0;

        do {

            try {

                do {

                    $this->log("Fetching search query.");

                    $search = $this->em->getRepository(TwitterSearch::class)
                        ->createQueryBuilder("ts")
                        ->where("ts.scheduled_at < :now")
                        ->orderBy("ts.scheduled_at", "ASC")
                        ->getQuery()
                        ->getOneOrNullResult();


                    if($search) {

                        $query = $search->getQuery();
                        $since_id = $search->getSinceId();
                        $max_id = $search->getMaxId();

                        $this->log("Searching for $query from $since_id until $max_id");
                        $rsp = $this->iterate($query, $since_id, $max_id);
                        $this->log("Search done.");

                        $search->setUpdateAt(new \Datetime());
                        $search->setScheduledAt(new \Datetime(date("Y-m-d H:i:s", time() + 15)));

                        $this->em->persist($search);

                        $this->em->flush();

                        $retry = 0;

                    }
                    
                    sleep(10);

                }
                while(true);

            }
            catch(\Exception $e) {

                $this->log("Search error $retry : " . $e->getMessage(), "error");
                
                if($retry >= 10) {

                    throw $e;

                }
                $retry++;
                sleep(2);

                
            }

        }
        while(true);

    }

    public function iterate($query, $since_id = null, $max_id = null, $greatest_id = null) 
    {

        $greatest_id = $since_id;
        
        $params = ["q" => $query, "result_type" => "recent", "count" => 100];

        if($since_id) {

            $params["since_id"] = $since_id - 1;

        }

        do {

            if($max_id) {
    
                $params["max_id"] = $max_id;

            }

            //var_dump("Calling search api.");

             $this->log("Calling search/tweets " . http_build_query($params));

            $rsp = $this->api->get("search/tweets", $params);



            try {

                $this->parseError($rsp);

            }
            catch(\Exception $e) {

                $this->log("Error " . $e->getMessage());
                return ["max_id" => $max_id, "since_id" => $since_id];

            }

            $statuses = $rsp->statuses;
            $count = count($statuses);

            $this->log("Collected $count Tweet.");

            if($count) {

                $firstTweet = $statuses[0];

                if($firstTweet->id_str > $greatest_id) {

                    $greatest_id = $firstTweet->id_str + 1;

                }

                $lastTweet = $statuses[count($statuses) - 1];

                $max_id = $lastTweet->id_str - 1;
                $this->log($max_id);

                

            }

            $this->pool->insert($statuses);

            $stmt = $this->db->getConnection()->prepare("update search set max_id = ?, since_id = ?, greatest_id = ?, updated_at = ? where id = ?");
            $stmt->execute([$max_id, $since_id, $greatest_id, date("Y-m-d H:i:s"), 1]);


        }
        while($count);

        return ["max_id" => null, "since_id" => $greatest_id];
        
        

    }

    public function parseError($response)
    {

        if(isset($response->errors)) {

            $error = $response->errors[0];
            throw new \Exception($error->message, $error->code);
            
        }

    }
}