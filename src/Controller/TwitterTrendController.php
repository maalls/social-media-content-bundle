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

    /**
     * @Route("/", name="twitter_trends")
     * @Template
     */
    public function indexAction(Request $request)
    {

        $rep = $this->getDoctrine()->getManager()->getRepository(TwitterTrend::class);

        $lastTrends = $rep->findBy([], ["datetime" => "DESC"], 1);

        if($lastTrends) {

            $lastTrend = $lastTrends[0];
            $trends = $rep->findBy(["datetime" => $lastTrend->getDatetime()], ["rank" => "ASC"]);

        }
        else {

            $trends = [];

        }

        return ["trends" => $trends, "datetime" => $lastTrend->getDatetime()];

    }

    /**
     * @Route("/{name}", name="twitter_trends_show")
     * @Template
     */
    public function showAction($name, Request $request)
    {

        $rep = $this->getDoctrine()->getManager()->getRepository(TwitterTrend::class);

        $trends = $rep

            ->createQueryBuilder("t")
            ->select("t.datetime, t.rank")
            ->where("t.name = :name")
            ->setParameter("name", $name)
            ->getQuery()
            ->getResult();

        return ["name" => $name, "trends" => $trends];

    }

}
