<?php

namespace Maalls\SocialMediaContentBundle\Service\Twitter;

use Maalls\SocialMediaContentBundle\Lib\Twitter\Api;
use \Doctrine\Common\Persistence\ObjectManager;

class EntityManager {


    protected $em;
    protected $api;

    public function __construct(Api $api, ObjectManager $em)
    {

        $this->api = $api;
        $this->em = $em;

    }

    public function generateTweetFromJson($t, $dataDatetime)
    {

        /*if(!isset($t->user)) {

            throw new \Exception("Hu?");

        }

        $tweet = $this->find($t->id_str);

        if(!$tweet) {

            $tweet = new Tweet();
            $tweet->setId($t->id_str);
            

        }

        if(!$tweet->getStatsUpdatedAt() || $tweet->getStatsUpdatedAt()->format("U") < $dataDatetime->format("U")) {

            $user = $this->getEntityManager()->getRepository(TwitterUser::class)->generateFromJson($t->user, $dataDatetime);
                
            $tweet->setUser($user);
            $tweet->setText($t->text);
            $tweet->setLanguage($t->lang);
            $tweet->setInReplyToStatusId($t->in_reply_to_status_id);
            $tweet->setIsQuoteStatus($t->is_quote_status);
            $tweet->setRetweetCount($t->retweet_count);
            $tweet->setFavoriteCount($t->favorite_count);
            $tweet->setStatsUpdatedAt($dataDatetime);
            $tweet->setPostedAt(new \Datetime($t->created_at));

            if(isset($t->retweeted_status)) {

                $retweetStatus = $this->generateFromJson($t->retweeted_status, $dataDatetime);
                $tweet->setRetweetStatus($retweetStatus);

            }

            $this->getEntityManager()->persist($tweet);

        }

        return $tweet;*/


    }

}