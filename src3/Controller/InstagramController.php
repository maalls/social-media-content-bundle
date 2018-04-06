<?php
// MetzWeb\Instagram\Instagram
namespace Maalls\SocialMediaContentBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/instagram")
 */
class InstagramController extends Controller
{


    public function __construct(\Maalls\SocialMediaContentBundle\Lib\Instagram\Factory $factory)
    {

        $this->factory = $factory;

        //parent::__construct();

    }   
    /**
     * @Route("/redirect", name="cms_instagram_redirect")
     * @Template
     */
    public function redirectAction(Request $request)
    {

        $instagram = $this->factory->createLoginApi();

        $code = $_GET['code'];
        $data = $instagram->getOAuthToken($code);
        
        var_dump($data);
        exit;
        
    }

    /**
     * @Route("/login", name="cms_instagram_login")
     * @Template
     */
    public function loginAction(Request $request)
    {

        return ["instagram" => $this->factory->createLoginApi()];

    }


}