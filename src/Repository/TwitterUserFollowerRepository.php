<?php

namespace Maalls\SocialMediaContentBundle\Repository;

use Maalls\SocialMediaContentBundle\Entity\TwitterUserFollower;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Api;

class TwitterUserFollowerRepository extends ServiceEntityRepository
{

    protected $api;

    public function __construct(RegistryInterface $registry, Api $api)
    {

        $this->api = $api;
        parent::__construct($registry, TwitterUserFollower::class);
    }


    public function generateFriendsFromTwitterUser($user)
    {

        if($user->getFriendsUpdatedAt()) {


        } 
        else {

            do {

                $cursor = null;
                $cursor = $this->generateFriendsFromCursor($user->getId(), $cursor);

            }
            while($cursor && $cursor != -1);

        }

    }

    public function generateFriendsFromCursor($userId, $cursor = null)
    {


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
            
                $rsp = $this->api->get("friends/ids", ["user_id" => $userId]);

                if($rsp->ids) {

                    $chunk = array_chunk($rsp->ids, 100);

                    foreach($chunk as $ids) {

                        $lookup = $this->api->get("users/lookup", ["user_id" => implode(",", $ids)]);


                        if(!is_array($lookup)) {

                            throw new \Exception(json_encode($lookup));

                        }

                        $fields = [
                            'id', 'name', 'screen_name', 
                            'description', 'lang', 'location', 
                            'verified', 'protected', 'followers_count', 
                            'friends_count', 'listed_count', 'updated_at'
                        ];

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

                            $followerParams[] = $userId;
                            $followerParams[] = $profile->id_str;
                            $followerParams[] = $now;
                            $followerParams[] = $now;

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

                            $followerParams[] = $userId;
                            $followerParams[] = $id;
                            $followerParams[] = $now;
                            $followerParams[] = $now;

                        }

                        $this->insert($conn, "twitter_user", $fields, $params, "id = id");

                        $followerFields = ["follower_id", "twitter_user_id", "created_at", "updated_at"];
                        $this->insert($conn, "twitter_user_follower", $followerFields, $followerParams, "updated_at = '$now'");

                    }


                }
                else {

                    // to handle.

                }

                // do stuff
                $conn->commit();

            } catch (\Exception $e) {

                $conn->rollBack();
                throw $e;

            }

            return isset($rsp->cursor) ? $rsp->cursor : -1;


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