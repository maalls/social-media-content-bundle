<?php

namespace Maalls\SocialMediaContentBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Maalls\SocialMediaContentBundle\Entity\TwitterTrend;


/**
 * @Route("/twitter/trends")
 */
class TwitterTrendController extends Controller
{


    private $woeid;


    public function __construct(\Maalls\SocialMediaContentBundle\Lib\Woeid $woeid)
    {

        $this->woeid = $woeid;

    }

    /**
     * @Route("/", name="twitter_trends")
     * @Template
     */
    public function indexAction(Request $request)
    {

        $locations = $this->woeid->getLocations();

        $rep = $this->getDoctrine()->getManager()->getRepository(TwitterTrend::class);
        $qb = $rep
            ->createQueryBuilder("t")
            ->select("t.name, t.woeid, min(t.rank) as rank")
            ->groupBy("t.name")
            ->addGroupBy("t.woeid")
            ->orderBy("t.name", "ASC");

        $search = $request->query->get("search");

        if($search) {

            $qb->andWhere("t.name LIKE :name")
                ->setParameter("name", "%$search%");

        }
        
        $query = $qb->getQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1),
            50
        );

        return ["locations" => $locations, "all" => $pagination, "search" => $search];

    }
    /**
     * @Route("/location/{woeid}", name="twitter_trends_location")
     * @Template
     */
    public function locationAction($woeid, Request $request)
    {

        $location = $this->woeid->findLocation($woeid);

        $rep = $this->getDoctrine()->getManager()->getRepository(TwitterTrend::class);

        $lastTrends = $rep->findBy(["woeid" => $woeid], [ "datetime" => "DESC"], 1);

        $lastTrend = null;
        $trends = [];

        if($lastTrends) {

            $lastTrend = $lastTrends[0];
            $trends = $rep->findBy(["woeid" => $woeid, "datetime" => $lastTrend->getDatetime()], ["rank" => "ASC"]);

        }
        
        $qb = $rep
            ->createQueryBuilder("t")
            ->select("t.name, t.woeid, min(t.rank) as rank")
            ->where("t.woeid = :woeid")->setParameter("woeid", $woeid)
            ->groupBy("t.name")
            ->orderBy("t.name", "ASC");

        $search = $request->query->get("search");

        if($search) {

            $qb->andWhere("t.name LIKE :name")
                ->setParameter("name", "%$search%");

        }
        
        $query = $qb->getQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1),
            50
        );

        return ["search" => $search, "recent" => $trends, "datetime" => $lastTrend ? $lastTrend->getDatetime() : null, "all" => $pagination, "location" => $location];

    }

    /**
     * @Route("/location/{woeid}/{name}", name="twitter_trends_show")
     * @Template
     */
    public function showAction($woeid, $name, Request $request)
    {

        $location = $this->woeid->findLocation($woeid);

        $rep = $this->getDoctrine()->getManager()->getRepository(TwitterTrend::class);

        $trends = $rep

            ->createQueryBuilder("t")
            ->select("t.datetime, t.rank")
            ->where("t.name = :name and t.woeid = :woeid")
            ->setParameters(["name" => $name, "woeid" => $woeid])
            ->getQuery()
            ->getResult();

        return ["name" => $name, "trends" => $trends, "location" => $location];

    }

    protected function getLocations()
    {

        return [
            ["woeid" => "1110809", "name" => "Japan"],
            ["woeid" => "1118370", "name" => "Tokyo"]
        ];

    }

    protected function findLocation($woeid)
    {

        foreach($this->getLocations() as $location) {

            if($location["woeid"] == $woeid) {

                return $location;

            }

        }

        return false;

    }


}
