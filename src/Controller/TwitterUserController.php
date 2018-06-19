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
 * @Route("/twitter/users")
 */
class TwitterUserController extends Controller
{


    

    /**
     * @Route("/", name="twitter_users")
     * @Template
     */
    public function indexAction(Request $request)
    {

        $qb = $this->getDoctrine()
            ->getManager()
            ->getRepository(TwitterUser::class)
            ->createQueryBuilder("u")
            ->where("u.lang = 'ja'")
            ->setMaxResults(10000)
            ->orderBy("u.score", "DESC");

        $search = $request->query->get("search");

        if($search) {

            $qb->andWhere("u.screen_name = :search")
                ->setParameter("search", $search);

        }

        
        $query = $qb->getQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1),
            200
        );

        if($pagination->getTotalItemCount() == 0 && $search) {

            $user = $this->getDoctrine()
                ->getManager()
                ->getRepository(TwitterUser::class)->generateFromScreenName($search);

            if($user) {

                return $this->redirectToRoute("twitter_users_show", ["id" => $user->getId()]);

            }

        } 
        elseif($pagination->getTotalItemCount() == 1) {

            foreach($pagination as $user) {

                return $this->redirectToRoute("twitter_users_show", ["id" => $user->getId()]);

            }
            

        }

        return ["pagination" => $pagination, "search" => $search];

    }

    /**
     * @Route("/{id}/timeline/update", name="twitter_users_timeline_update")
     * @Template
     */
    public function updateTimeline(TwitterUser $user) 
    {

        $this->getDoctrine()->getManager()->getRepository(TwitterUser::class)->updateTimeline($user, false);

        return $this->redirectToRoute("twitter_users_show", ["id" => $user->getId()]);

    }

    /**
     * @Route("/{id}/followers", name="twitter_users_followers")
     * @Template
     */
    public function followersAction(TwitterUser $user, Request $request)
    {

        $followers_count = $user->getFollowersCount();
        if($followers_count > 100000 && !$user->getFollowersUpdatedAt()) {

            throw new \Exception("To many followers.");

        }

        $rep = $this->getDoctrine()->getManager()->getRepository(TwitterUserFollower::class);

        $rep->generateFollowersFromTwitterUser($user);
        
        $query = $this->getDoctrine()->getManager()->getRepository(TwitterUserFollower::class)->createQueryBuilder("f")

            ->where("f.twitterUser = :i")->setParameter("i", $user)
            ->join("f.follower", "u")
            ->addSelect("u")
            ->getQuery();

        $paginator  = $this->get('knp_paginator');


        $count = $this->getDoctrine()->getManager()->getRepository(TwitterUserFollower::class)->createQueryBuilder("f")
            ->where("f.twitterUser = :i")->setParameter("i", $user)
            ->select("count(f)")
            ->getQuery()
            ->getSingleScalarResult();

        $query->setHint("knp_paginator.count", $count);
        $pagination = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1),
            500
        );        

        return ['pagination' => $pagination, 'user' => $user];

    }

    /**
     * @Route("/{id}/following", name="twitter_users_following")
     * @Template
     */
    public function followingAction(TwitterUser $user, Request $request)
    {


        $friends_count = $user->getFriendsCount();

        if($friends_count > 5000) {

            throw new \Exception("To many friends.");

        }

        $rep = $this->getDoctrine()->getManager()->getRepository(TwitterUserFollower::class);

        $rep->generateFriendsFromTwitterUser($user);
        
        $query = $this->getDoctrine()->getManager()->getRepository(TwitterUser::class)->createQueryBuilder("u")
            ->join("u.followers", "f")
            ->where("f.follower = :u")->setParameter("u", $user)
            ->getQuery();

        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1),
            500
        );        

        return ['pagination' => $pagination, 'user' => $user];

    }

    /**
     * @Route("/{id}", name="twitter_users_show")
     * @Template
     */
    public function showAction($id, Request $request)
    {

        $user = $this->getDoctrine()
                ->getManager()
                ->getRepository(TwitterUser::class)
                ->createQueryBuilder("u")
                ->where("u.id = :id")->setParameter("id", $id)
                ->select("u")->getQuery()->getSingleResult();

        $stats = [
            "stats" => [
                ["name" => "Followers", "value" => $user->getFollowersCount(), "link" => $this->generateUrl("twitter_users_followers", ["id" => $user->getid()])],
                ["name" => "Following", "value" => $user->getFriendsCount(), "link" => $this->generateUrl("twitter_users_following", ["id" => $user->getid()])],
                ["name" => "Listed", "value" => $user->getListedCount(), "link" => $this->generateUrl("twitter_users_following", ["id" => $user->getid()])],
                ["name" => "RT / Post", "value" => $user->getRetweetMedian(), "link" => ""],
                ["name" => "Fav / Post", "value" => $user->getFavoriteMedian(), "link" => ""],
                ["name" => "%RT", "value" => round($user->getRetweetRate()*100,1), "link" => ""]
            ]
        ];

        $timeline = [];
        $retweeters = [];

        if($user->getTimelineUpdatedAt()) {

            $qb = $this->getDoctrine()
                ->getManager()
                ->getRepository(Tweet::class)
                ->createQueryBuilder("t")
                ->select("t")
                ->where("t.user = :user")->setParameter("user", $user);

            $sort = $request->query->get("sort", "id");

            $timeline = $qb
                ->addOrderBy("t." . $sort, "DESC")
                ->getQuery()
                ->getResult();

            

            $retweets = [];
            $favorites = [];
            
            foreach($timeline as $tweet)
            {

                if($tweet->getInReplyToStatusId()) {



                }
                elseif(preg_match("/^@/", $tweet->getText())) {



                }
                elseif($tweet->getRetweetStatus()) {


                }
                else {

                    $retweets[] = $tweet->getRetweetCount();
                    $favorites[] = $tweet->getFavoriteCount();

                }

            }

            $retweets = array_reverse($retweets);

            $stats["trends"] = [
                    "retweets" => $retweets,
                    "favorites" => $favorites
            ];
                      
        }

        return ["user" => $user, "timeline" => $timeline, "stats" => $stats];
    }

}