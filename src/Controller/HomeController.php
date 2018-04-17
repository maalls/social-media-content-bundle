<?php

namespace Maalls\SocialMediaContentBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity\TwitterUser;

/**
 * @Route("/")
 */
class HomeController extends Controller
{


    /**
     * @Route("/", name="smc_home")
     * @Template
     */
    public function indexAction(Request $request)
    {

        return $this->redirectToRoute("twitter_users");

    }


}