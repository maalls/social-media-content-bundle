<?php

namespace Maalls\SocialMediaContentBundle\Repository;

use Maalls\SocialMediaContentBundle\Entity\TwitterUser;
use Maalls\SocialMediaContentBundle\Entity\Tweet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Api;
use Maalls\SocialMediaContentBundle\Repository\LoggableServiceEntityRepository;
use Maalls\SocialMediaContentBundle\Lib\SqlHelper;

/**
 * @method TwitterUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method TwitterUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method TwitterUser[]    findAll()
 * @method TwitterUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TwitterUserRepository extends LoggableServiceEntityRepository
{

    protected $api;

    public function __construct(RegistryInterface $registry, Api $api)
    {
        $this->api = $api;
        parent::__construct($registry, TwitterUser::class);
    }

    public function setLogger($logger)
    {

        $this->api->setLogger($logger);
        parent::setLogger($logger);

    }

    public function setApi($api)
    {

        $this->api = $api;

    }

    public function updateAllTimelines($followers_count = 10000, $taskId = null)
    {
        $taskId = $taskId ? $taskId : str_pad(getmypid(), 6, "0");

        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $conn->getConfiguration()->setSQLLogger(null);
        $totalStmt = $conn->prepare("select count(id) from twitter_user where timeline_updated_at is null and status = 200 and lang = 'ja' and followers_count >= $followers_count and protected = 0");
        $lockStmt = $conn->prepare("update twitter_user set status = ? where timeline_updated_at is null and status = 200 and lang = 'ja' and followers_count >= $followers_count and protected = 0 order by followers_count desc limit 20");
        
        $done = 0;
        $lastTotalUpdate = null;

        $exitFunction = function() use ($conn, $taskId) {

            
            $this->log("Command shut down, releasing statues for $taskId");
            $this->unlockUsers($taskId);
            exit();

        };

        declare(ticks=1);
        pcntl_signal(SIGINT,  $exitFunction);

        do {

            try {
                $em->clear(TwitterUser::class);
                $em->clear(Tweet::class);
                $em->clear();
                gc_collect_cycles();

                if(!$lastTotalUpdate || $lastTotalUpdate + 2*60 < time()) {

                    $totalStmt->execute();
                    $total = $totalStmt->fetch(\Doctrine\ORM\Query::HYDRATE_SCALAR);
                    $total = $total[0];
                    $lastTotalUpdate = time();

                }

                $lockStmt->execute([$taskId]);
                
                $users = $this->createQueryBuilder("u")
                    ->where("u.status = :status")
                    ->setParameter("status", $taskId)
                    ->getQuery()
                    ->getResult();
                
                if($users) {
                
                    $this->log($taskId . " " . number_format($done) . "/" . number_format($total) . " of user over $followers_count followers, processing " . count($users) . " users in batch.");    
                    $this->updateTimelines($users);
                    $done += count($users);
                    $this->log("done");

                }
                

            }
            catch(\Doctrine\DBAL\Exception\DeadlockException $e) {

                $this->log("Deadlock found while updating all timelines, trying again : " . $e->getMessage());

                sleep(2);

            }
            catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {

                $this->log("Might didn't liked multi-tread, trying again : " . $e->getMessage());
                sleep(2);

            }

            catch(\Exception $e) {

                $this->log(get_class($e) . " " . $e->getCode() . " : " . $e->getMessage(), "error");
                $this->unlockUsers($taskId);
                throw $e;

            }

            $this->getEntityManager()->clear(Tweet::class);
            $this->getEntityManager()->clear(TwitterUser::class);


        }
        while($users);

        pcntl_signal(SIGINT,  SIG_DFL);

        return $done;


    }

    public function unlockUsers($taskId)
    {

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("update twitter_user set status = 200 where status = ?");
        $stmt->execute([$taskId]);

    }

    public function updateTimelines($users, $use_cache = true)
    {

        foreach($users as $user) {

            $this->updateTimeline($user, $use_cache);

        }

    }

    public function updateTimeline($user, $use_cache = true)
    {

        $this->log("Updating timeline for user ID " . $user->getId() . " " . $user->getScreenName() . " " . $user->getFollowersCount() . " followers");
        $em = $this->getEntityManager();
        $tweetRep = $em->getRepository(Tweet::class);

        $retry = 5;

        do {
            $timeline = $this->api->get("statuses/user_timeline", ["user_id" => $user->getId(), "count" => 200], $use_cache);
            
            if(isset($timeline->errors)) {

                $error = $timeline->errors[0]; 

                switch($error->code) {

                    case 136:
                        sleep(5);
                        $retry--;
                        break;
                    default:
                        $retry = 0;

                }

            }
            else {

                $retry = 0;

            }
        }
        while($retry);

        $dataDatetime = $this->api->getApiDatetime();

        if(!isset($timeline->errors) && !isset($timeline->error)) {

                $retweets = [];
                $favorites = [];
                $periods = [];
                $previous = null;
                $retweetCount = 0;

                foreach($timeline as $l => $t) {

                    $tweet = $tweetRep->generateFromJson($t, $dataDatetime);

                    if($tweet->getInReplyToStatusId()) {

                        // TODO: how to deal with account with lot of reply?

                    }
                    elseif(preg_match("/^@/", $tweet->getText())) {



                    }
                    elseif($tweet->getRetweetStatus()) {

                        $retweetCount++;

                    }
                    else {
                    
                        $retweets[] = $tweet->getRetweetCount();
                        $favorites[] = $tweet->getFavoriteCount();

                        

                    }

                    if($previous) {

                        $periods[] =  $previous->getPostedAt()->format("U") - $tweet->getPostedAt()->format("U");

                    }

                    $previous = $tweet;

                }

                $retweetRate = $timeline ? $retweetCount / count($timeline) : 0;
                sort($retweets);
                $retweetMedian =  $retweets ? $retweets[floor(count($retweets) / 2)] : 0;
                sort($favorites);
                $favoriteMedian = $favorites ? $favorites[floor(count($favorites) / 2)] : 0; 
                sort($periods);
                $periodMedian =  $periods ? $periods[floor(count($periods) / 2)] : 0;

                $user->setRetweetMedian($retweetMedian);
                $user->setFavoriteMedian($favoriteMedian);
                $user->setPostPeriodMedian($periodMedian);
                $user->setRetweetRate($retweetRate);
                $user->setScore($retweetMedian);
                $user->setTimelineUpdatedAt($dataDatetime);
                $user->setStatus(200);
                $em->persist($user);

                $em->flush();

        }
        else {

            if(isset($timeline->errors)) {

                throw new \Exception($timeline->errors[0]->message, $timeline->errors[0]->code);

            }
            elseif(isset($timeline->error) && $timeline->error == "Not authorized.") {

                $this->log("Protected user.");
                $user->setStatus(200);
                $user->setProtected(1);
                $user->setTimelineUpdatedAt($dataDatetime);
                $em->flush();

            }
            else {



                throw new \Exception("Unexpected error : " . json_encode($timeline));

            }

        }

    }

    public function updateProfiles($taskId = null)
    {


        $taskId = $taskId ? $taskId : str_pad(getmypid(), 6, "0");

        $this->log("Task ID: " . $taskId);

        $conn = $this->getEntityManager()->getConnection();
        $conn->getConfiguration()->setSQLLogger(null);
        
        $totalStmt = $conn->prepare("select count(id) from twitter_user where profile_updated_at is null and status = 200");
                
        $lockStmt = $conn->prepare("update twitter_user set status = ? where profile_updated_at is null and status = 200  limit 1000");

        $fetchStmt = $conn->prepare("select id from twitter_user where status = ?");
        $done = 0;

        $lastTotalUpdate = null;


        declare(ticks=1);
        pcntl_signal(SIGINT,  function() use ($conn, $taskId) {

            $stmt = $conn->prepare("update twitter_user set status = 200 where status = ?");
            $stmt->execute([$taskId]);
            $this->log("Command shut down, releasing statues for $taskId");
            exit();

        });

        do {

            try {
                gc_collect_cycles();

                if(!$lastTotalUpdate || $lastTotalUpdate + 2*60 < time()) {

                    $totalStmt->execute();
                    $total = $totalStmt->fetch(\Doctrine\ORM\Query::HYDRATE_SCALAR);
                    $total = $total[0];
                    $lastTotalUpdate = time();

                }

                $lockStmt->execute([$taskId]);
                $fetchStmt->execute([$taskId]);

                $ids = [];
                while($row = $fetchStmt->fetch(\Doctrine\ORM\Query::HYDRATE_SCALAR)) {

                    $ids[] = $row[0];

                }

                
                if($ids) {
                
                    $this->log($taskId . " " . number_format($done) . "/" . number_format($total) . " processing. ");    
                    $this->updateProfilesFromUserIds($ids);
                    $done += count($ids);
                    $this->log("done");

                }
                else {

                    sleep(5);

                }

            }
            catch(\Doctrine\DBAL\Exception\DeadlockException $e) {

                $this->log("Deadlock found while updating profiles, trying again : " . $e->getMessage());
                sleep(2);

            }
            catch(\Exception $e) {

                $this->log(get_class($e) . " " . $e->getCode() . " : " . $e->getMessage(), "error");
                throw $e;

            }


        }
        while($ids);

        pcntl_signal(SIGINT,  SIG_DFL);

        return $done;

    }

    public function updateProfilesFromUserIds($ids)
    {

        $chunks = array_chunk($ids, 100);

        foreach($chunks as $chunk) {


            $lookup = $this->api->get("users/lookup", ["user_id" => implode(",", $chunk)]);
            $profileUpdatedAt = $this->api->getApiDatetime();

            if(!is_array($lookup)) {

                throw new \Exception(json_encode($lookup));

            }

            $conn = $this->getEntityManager()->getConnection();


            $stmt = $conn->prepare("
                update 
                    twitter_user 
                set 
                    name = ?, screen_name = ?, description = ?, 
                    lang = ?, location = ?, verified = ?, protected = ?, 
                    followers_count = ?, friends_count = ?, listed_count = ?,
                    updated_at = ?, profile_updated_at = ?, status = 200
                where 
                    id = ?");

            $notFoundStmt = $conn->prepare("
                update 
                    twitter_user
                set 
                    status = 404,
                    profile_updated_at = ?
                where 
                    id = ?

            ");

            $now = date("Y-m-d H:i:s");
            $founds = [];

            foreach($lookup as $profile) {

                $params = [];
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
                $params[] = $profile->id_str;

                $stmt->execute($params);

                $founds[] = $profile->id_str;

            }

            $notFounds = array_diff($chunk, $founds);

            $this->log(count($founds) . " found, " . count($notFounds) . " not found. "  . round(memory_get_usage(true) / 1024/1024) . "Mb used.");

            foreach($notFounds as $id) {

                $notFoundStmt->execute([$profileUpdatedAt->format("Y-m-d H:i:s"), $id]);

            }

        }

    }


    

    public function generateFromScreenName($screen_name)
    {

        $user = $this->findOneBy(["screen_name" => $screen_name]);

        if(!$user) {

            $rsp = $this->api->get("users/lookup", ["screen_name" => $screen_name]);
            $dataDatetime = $this->api->getApiDatetime();

            if(isset($rsp->errors)) {

                if($rsp->errors[0]->code == 17) {

                    $user = false;

                }
                else {

                    throw new \Exception($rsp->errors[0]->message, $rsp->errors[1]->code);

                }

            }
            else {

                $profile = $rsp[0];
                $user = $this->generateFromJson($profile, $dataDatetime);
                $em = $this->getEntityManager();
                $em->persist($user);
                $em->flush();


            }
        }

        return $user;
        
    }


    public function generateFromJson($profile, $dataDatetime)
    {

        //$this->log("Generating user from json profile.");
        $user = $this->find($profile->id_str);

        if(!$user) {
    
            //$this->log("New user found.");
            $user = new TwitterUser();

        }

        if(!$user->getProfileUpdatedAt() || $user->getProfileUpdatedAt()->format("U") < $dataDatetime->format("U")) {

            //$this->log("hydrating user information");
            $this->hydrateJson($user, $profile, $dataDatetime);
            $this->getEntityManager()->persist($user);

        }
        
        return $user;

    }

    public function hydrateJson(TwitterUser $user, $profile, $dataDatetime)
    {

        $user->setId($profile->id_str);
        $user->setUpdatedAt(new \Datetime());
        $user->setScreenName($profile->screen_name);
        $user->setName($profile->name);
        $user->setDescription($profile->description);
        $user->setProtected($profile->protected);
        $user->setFollowersCount($profile->followers_count);
        $user->setFriendsCount($profile->friends_count);
        $user->setListedCount($profile->listed_count);
        $user->setVerified($profile->verified);
        $user->setLang($profile->lang);
        $user->setProfileUpdatedAt($dataDatetime);
        
        $user->setLocation($profile->location);

        return $user;

    }
    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('t')
            ->where('t.something = :value')->setParameter('value', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
