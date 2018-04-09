<?php

namespace Maalls\SocialMediaContentBundle\Repository;

use Maalls\SocialMediaContentBundle\Entity\TwitterUserFollower;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Api;

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

            $cursor = null;
            
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
        /* rsp if protected class stdClass#473 (2) {
                  public $request =>
                  string(21) "/1.1/friends/ids.json"
                  public $error =>
                  string(15) "Not authorized."
                }
            */
            $conn = $this->getEntityManager()->getConnection();
            $conn->beginTransaction();

            try{

                $params = ["user_id" => $userId, "count" => 5000];
                if($cursor) {

                    $params["cursor"] = $cursor;

                }

                $rsp = $this->api->get($relation . "/ids", $params);
                $relationUpdatedAt = $this->api->getApiDatetime();


                if($rsp->ids) {

                    $chunks = array_chunk($rsp->ids, 100);

                    foreach($chunks as $k => $ids) {

                        $this->log("collecting chunk " . $k . " / " . count($chunks) . " memory: " . round(memory_get_usage(true)/1024/1024) . "Mb");
                        $lookup = $this->api->get("users/lookup", ["user_id" => implode(",", $ids)]);
                        $profileUpdatedAt = $this->api->getApiDatetime();


                        if(!is_array($lookup)) {

                            throw new \Exception(json_encode($lookup));

                        }

                        $fields = [
                            'id', 'name', 'screen_name', 
                            'description', 'lang', 'location', 
                            'verified', 'protected', 'followers_count', 
                            'friends_count', 'listed_count', 'updated_at',
                            'profile_updated_at'
                        ];

                        $followerFields = ["follower_id", "twitter_user_id", "created_at", "updated_at"];

                        $params = [];
                        $now = date("Y-m-d H:i:s");

                        $founds = [];




                        foreach($lookup as $profile) {

                            $params[] = $profile->id_str;
                            $params[] = $profile->name;
                            $params[] = $profile->screen_name;
                            $params[] = $profile->description;
                            $params[] = $profile->lang;
                            $params[] = $profile->location;
                            $params[] = $profile->verified ? 1: 0;
                            $params[] = $profile->protected ? 1 : 0;
                            $params[] = $profile->followers_count;
                            $params[] = $profile->friends_count;
                            $params[] = $profile->listed_count;
                            $params[] = $now;
                            $params[] = $profileUpdatedAt->format("Y-m-d H:i:s");

                            switch($relation) {
                                case 'friends':
                                    $followerParams[] = $userId;
                                    $followerParams[] = $profile->id_str;
                                    break;
                                case 'followers':

                                    $followerParams[] = $profile->id_str;
                                    $followerParams[] = $userId;
                                    break;
                                default:
                                    throw new Exception("Invalid relation $relation");
                                    
                            }

                            $followerParams[] = $now;
                            $followerParams[] = $profileUpdatedAt->format("Y-m-d H:i:s");

                            $founds[] = $profile->id_str;

                            

                        }

                        $notFounds = array_diff($ids, $founds);

                        foreach($notFounds as $id) {

                            $params[] = $id;
                            $params[] = '';
                            $params[] = '';
                            $params[] = '';
                            $params[] = '';
                            $params[] = '';
                            $params[] = null;
                            $params[] = null;
                            $params[] = '';
                            $params[] = '';
                            $params[] = '';
                            $params[] = $now;
                            $params[] = $profileUpdatedAt->format("Y-m-d H:i:s");

                            switch($relation) {
                                case 'friends':
                                    $followerParams[] = $userId;
                                    $followerParams[] = $id;
                                    break;
                                case 'followers':

                                    $followerParams[] = $id;
                                    $followerParams[] = $userId;
                                    break;
                                default:
                                    throw new Exception("Invalid relation $relation");
                                    
                            }


                            $followerParams[] = $now;
                            $followerParams[] = $profileUpdatedAt->format("Y-m-d H:i:s");

                        }

                        $this->insert($conn, "twitter_user", $fields, $params, "id = id");

                        
                        $this->insert($conn, "twitter_user_follower", $followerFields, $followerParams, "updated_at = '$now'");

                    }


                    $this->log(count($rsp->ids) . " followers added.");

                }
                else {

                    // to handle.
                    var_dump($rsp);
                    throw new \Exception(json_encode($rsp));

                }

                $stmt = $conn->prepare("update twitter_user set " . $relation . "_updated_at = ? where id = ?");
                $stmt->execute([$relationUpdatedAt->format("Y-m-d H:i:s"), $user->getId()]);

                // do stuff
                $conn->commit();

            } catch (\Exception $e) {

                $conn->rollBack();
                throw $e;

            }

            
            if(isset($rsp->next_cursor_str)) {

                $this->log("Next cursor : " . $rsp->next_cursor_str);

                return $rsp->next_cursor_str;

            } 
            else {

                $this->log("No more cursor.");
             
            }


    }

    public function insert($conn, $table, $fields, $parameters, $onDuplicateKey = '') {

        $entryCount = count($parameters) / count($fields);

        if($entryCount != round(count($parameters) / count($fields))) {

            throw new \Exception("Number of Fields and parameters not compatible.");

        }

        $values = trim(str_repeat("?,", count($fields)), ",");
        $values = trim(str_repeat("($values),", $entryCount), ",");

        $onDuplicateKey = $onDuplicateKey ? " ON DUPLICATE KEY UPDATE $onDuplicateKey" : '';

        $query = "insert into $table (`" . implode("`, `", $fields) . "`) values " . $values . " $onDuplicateKey";

        $stmt = $conn->prepare($query);
        $stmt->execute($parameters);

        return $stmt;

    }


}