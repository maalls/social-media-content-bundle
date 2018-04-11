<?php

namespace Maalls\SocialMediaContentBundle\Repository;

use Maalls\SocialMediaContentBundle\Entity\TwitterUserFollower;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Api;
use Maalls\SocialMediaContentBundle\Lib\SqlHelper;

class TwitterUserFollowerRepository extends LoggableServiceEntityRepository
{

    protected $api;

    public function __construct(RegistryInterface $registry, Api $api)
    {

        $this->api = $api;
        parent::__construct($registry, TwitterUserFollower::class);
    }


    public function generateFollowersFromTwitterUser($user, $force = false)
    {

        if($user->getFollowersUpdatedAt() && !$force) {

            $this->log("Followers already collected.");

        } 
        else {

            $cursor = 1440771217574818876;

            do {

                
                $cursor = $this->generateFromCursor($user, $cursor, "followers");

            }
            while($cursor && $cursor != -1);

        }

    }




    public function generateFriendsFromTwitterUser($user)
    {

        if($user->getFriendsUpdatedAt()) {


        } 
        else {

            $cursor = null;

            do {    
                
                $cursor = $this->generateFriendsFromCursor($user, $cursor);

            }
            while($cursor && $cursor != -1);

        }

    }


    public function generateFriendsFromCursor($user, $cursor)
    {

        return $this->generateFromCursor($user, $cursor, "friends");

    }

    // relation = friends, followers
    public function generateFromCursor($user, $cursor = null, $relation)
    {

        $this->log("Collecting from cursor " . $cursor);
        $userId = $user->getId();
        
        $params = ["user_id" => $userId, "count" => 5000];
        if($cursor) {

            $params["cursor"] = $cursor;

        }

        $rsp = $this->api->get($relation . "/ids", $params);
        $relationUpdatedAt = $this->api->getApiDatetime();


        if(isset($rsp->ids)) {

            $this->log("Insering " . count($rsp->ids) . " " . $relation . " " . round(memory_get_usage(true) / 1024/1024) . "Mb used.");
            $this->insertRelation($user, $rsp->ids, $relation, $relationUpdatedAt);
            
        }
        else {

            // to handle.
            var_dump($rsp);
            throw new \Exception(json_encode($rsp));

        }

        if(isset($rsp->next_cursor_str)) {

            $this->log("Next cursor : " . $rsp->next_cursor_str);

            return $rsp->next_cursor_str;

        } 
        else {

            $this->log("No more cursor.");
         
        }


    }

    public function insertRelation($user, $ids, $relation, $relationUpdatedAt)
    {


        $conn = $this->getEntityManager()->getConnection();
        $conn->getConfiguration()->setSQLLogger(null);
        $conn->beginTransaction();

        $fields = ["id", "updated_at", "status"];
        $followerFields = ["follower_id", "twitter_user_id", "created_at", "updated_at"];
        $now = date("Y-m-d H:i:s");

        $params = [];
        $followerParams = [];

        foreach($ids as $id) {

            $params[] = $id;
            $params[] = $now;
            $params[] = 200;

            switch($relation) {
                case 'friends':
                    $followerParams[] = $user->getId();
                    $followerParams[] = $id;
                    break;
                case 'followers':
                    $followerParams[] = $id;
                    $followerParams[] = $user->getId();
                    break;
                default:
                    throw new Exception("Invalid relation $relation");

            }
            $followerParams[] = $now;
            $followerParams[] = $now;

        }

        $retry = 10;
        do {
            try {
            
                SqlHelper::insert($conn, "twitter_user", $fields, $params, "id = id");
                SqlHelper::insert($conn, "twitter_user_follower", $followerFields, $followerParams, "updated_at = '$now'");

                $stmt = $conn->prepare("update twitter_user set " . $relation . "_updated_at = ? where id = ?");
                $stmt->execute([$relationUpdatedAt->format("Y-m-d H:i:s"), $user->getId()]);
                $conn->commit();

                return;

            }
            catch(\Doctrine\DBAL\Exception\DeadlockException $e) {

                $retry--;
                
                if($retry) {
                
                    $this->log("deadlock found, trying again in few sec.");
                    sleep(2);
                
                }

            }
            catch(\Exception $e) {

                $conn->rollBack();
                throw $e;            

            }

        }
        while($retry);

        throw new \Exception("Too many retry.");

    }

}