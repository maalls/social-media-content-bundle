<?php

namespace Maalls\SocialMediaContentBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Maalls\SocialMediaContentBundle\Entity\TwitterUser;
use Maalls\SocialMediaContentBundle\Entity\Tweet;
use Maalls\SocialMediaContentBundle\Entity\TwitterUserFollower;


/**
 * @Route("/twitter/tweets")
 * @Template
 */
class TweetController extends Controller
{

    /**
     * @Route("/", name="twitter_tweets")
     * @Template
     */
    public function index(Request $request)
    {

        $qb = $this->getDoctrine()
            ->getManager()
            ->getRepository(Tweet::class)
            ->createQueryBuilder("t")
            ->orderBy("t.id", "DESC");
        $default_params = ["id" => ""];
        $q = $request->query->get("q", $default_params);

        $this->addUserQuery($qb, $q);

        $query = $qb->getQuery()->setHint('knp_paginator.count', 300000);
        
        $paginator  = $this->get('knp_paginator');
        
        $pagination = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1),
            200
        );

        if(count($pagination) == 0 && $q["id"]) {

            $tweet = $this->getDoctrine()
                ->getManager()
                ->getRepository(Tweet::class)->generateFromStatusId($q["id"]);

            if($tweet) {

                return $this->redirectToRoute("twitter_tweets_show", ["id" => $tweet->getId()]);

            }

        } 


        return ["pagination" => $pagination, "q" => $q];

    }

    /**
     * @Route("/twitter/tweets/{id}", name="twitter_tweets_show")
     * @Template
     */
    public function show(Tweet $tweet)
    {

        return ["tweet" => $tweet];


    }

    /**
     * @Route("/twitter/tweets/{id}/retweets", name="twitter_tweets_retweets")
     */
    public function retweets(Tweet $tweet)
    {

        $rep = $this->getDoctrine()->getManager()->getRepository(Tweet::class);

        $rep->updateRetweets($tweet);

        return $this->redirectToRoute("twitter_tweets_show", ["id" => $tweet->getId()]);

    }

    public function addUserQuery($qb, $query)
    {


        foreach($query as $key => $value) {

            if($value) {
            
                $qb->andWhere("t.$key = :$key")->setParameter($key, $value);

            }

        }


    }

}
