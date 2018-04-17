<?php

namespace Maalls\SocialMediaContentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Maalls\SocialMediaContentBundle\Lib\LoggerOutputAdapter;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Search;
use Maalls\SocialMediaContentBundle\Entity\TwitterUser;
use Maalls\SocialMediaContentBundle\Entity\TwitterUserFollower;
use Maalls\SocialMediaContentBundle\Entity\Tweet;

class TmpCommand extends Command
{

    public function __construct(\Doctrine\Common\Persistence\ObjectManager $em, \Maalls\SocialMediaContentBundle\Lib\Twitter\Api $api)
    {

        parent::__construct();
        $this->em = $em;
        $this->api = $api;
        $this->stmt = $this->em->getConnection()->prepare("update twitter_user set url = ? where id = ?");

    }

    protected function configure()
    {
        $this
        ->setName('smc:tmp')
        ->setDescription('Tmp command.')
        ->addArgument("user_id", InputArgument::OPTIONAL, 'The user id.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->em;

        

        $query = $em->getRepository(TwitterUser::class)->createQueryBuilder("u")
            ->where("u.status = 200 and u.lang = 'ja' and u.score is null and u.followers_count > 10000 and u.timeline_updated_at is not null")
            ->setMaxResults(200)
            ->orderBy("u.followers_count", "desc")
            ->getQuery();

        do {

            if($id = $input->getArgument("user_id")) {

                $user = $em->getRepository(TwitterUser::class)->find($id);
                $users = [$user];

            }
            else {
            
                $users = $query->getResult();
                $output->writeln("Processing " . count($users) . " users.");
           
            }

            foreach($users as $user) {

                $output->writeln($user->getScreenName());

                $retweets = [];
                $favorites = [];
                $periods = [];
                $previous = null;
                $retweetCount = 0;

                $tweets = $em->getRepository(Tweet::class)->createQueryBuilder("t")->select("t,rt")->where("t.user = :user")->leftJoin("t.retweet_status", "rt")->setParameter("user", $user)->orderBy("t.id", "DESC")->getQuery()->getArrayResult();

                $output->writeln("tweets : " . count($tweets));

                foreach($tweets as $l => $tweet) {


                    if($tweet["in_reply_to_status_id"]) {

                        // TODO: how to deal with account with lot of reply?

                    }
                    elseif(preg_match("/^@/", $tweet["text"])) {



                    }
                    elseif(isset($tweet["retweet_status"]) && $tweet["retweet_status"]) {

                        $retweetCount++;

                    }
                    else {
                    
                        //$output->writeln($tweet["id"] . " retweet " . $tweet["retweet_count"]);
                        $retweets[] = $tweet["retweet_count"];
                        $favorites[] = $tweet["favorite_count"];

                    }

                    if($previous) {

                        $periods[] =  $previous["posted_at"]->format("U") - $tweet["posted_at"]->format("U");

                    }

                    $previous = $tweet;

                }

                $retweetRate = count($tweets) ? $retweetCount / count($tweets) : 0;
                sort($retweets);
                $retweetMedian =  $retweets ? $retweets[floor(count($retweets) / 2)] : 0;
                sort($favorites);
                $favoriteMedian = $favorites ? $favorites[floor(count($favorites) / 2)] : 0; 
                sort($periods);
                $periodMedian =  $periods ? $periods[floor(count($periods) / 2)] : 0;

                

                $user->setRetweetMedian($retweetMedian);
                $user->setScore($retweetMedian);
                $user->setFavoriteMedian($favoriteMedian);
                $user->setPostPeriodMedian($periodMedian);
                $user->setRetweetRate($retweetRate);
                $user->setStatus(200);
                $em->persist($user);
                $em->flush();

            }
            
            $em->clear();
        }
        while(count($users) == 200);


    }

    public function collectEntities($user, $dataDatime)
    {

        

        /*if(isset($user->status) && !isset($user->status->retweeted_status)) {

            $u = clone $user;
            $status = $u->status;
            $u->status = null;
            $status->user = $u;
            $t = $this->em->getRepository(Tweet::class)->generateFromJson($status, $dataDatime);
        
            echo $user->screen_name . " : updating status" . PHP_EOL;
            $this->em->flush();
            
        }*/

        $entities = $user->entities;

        if(isset($entities->description)) {

            if(isset($entities->description->urls)) {

                if($entities->description->urls) {

                    //var_dump($entities->description->urls);
                    //exit;

                }


            }

        }

        if(isset($entities->url)) {

            $count = count($entities->url->urls);

            $url = $entities->url->urls[0]->expanded_url;
            echo $user->screen_name . " (" . $count . ") : " . $url .  PHP_EOL;
            if($count > 1) {

                var_dump($user);
                exit;

            }
            else {

                $this->stmt->execute([$url, $user->id_str]);
                

            }

        }

        
    }

}