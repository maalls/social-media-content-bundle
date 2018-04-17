<?php

namespace Maalls\SocialMediaContentBundle\Repository;

use Maalls\SocialMediaContentBundle\Entity\Tweet;
use Maalls\SocialMediaContentBundle\Entity\TwitterUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Maalls\SocialMediaContentBundle\Repository\LoggableServiceEntityRepository;
/**
 * @method Tweet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tweet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tweet[]    findAll()
 * @method Tweet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TweetRepository extends LoggableServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tweet::class);
    }


    public function countAll()
    {

        return $this->createQueryBuilder("t")
                    ->select("count(t)")
                    ->getQuery()
                    ->getSingleScalarResult();

    }


    public function generateFromJsons($array, $dataDatetime)
    {

        $tweets = [];

        foreach($array as $json) {

            $tweets[] = $this->generateFromJson($json, $dataDatetime);

        }

        return $tweets;

    }

    public function generateFromJson($t, $dataDatetime)
    {
        if(!isset($t->user)) {

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

            //$this->log("Persisting tweet " . $tweet->getId(). " with author " . $tweet->getUser()->getId());
            $this->getEntityManager()->persist($tweet);
            //$this->log("Tweet persisted.");
        }

        return $tweet;


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
