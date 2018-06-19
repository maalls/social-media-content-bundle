<?php

namespace Maalls\SocialMediaContentBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Maalls\SocialMediaContentBundle\Entity\TwitterUser;
use Maalls\SocialMediaContentBundle\Entity\Tag;
use Maalls\SocialMediaContentBundle\Entity\UserTag;
/**
 * @Route("/users")
 */
class UserTagController extends Controller
{


    /**
     * @Route("/{user}/tags/select", name="users_tags_select")
     * @Template
     */
    public function select(TwitterUser $user, Request $request)
    {

        $tags = $this->getDoctrine()->getManager()->getRepository(Tag::class)->findAll();
        return ["user" => $user, "tags" => $tags];

    }

    /**
     * @Route("/{user}/tags/{tag}/toggle", name="users_tags_toggle")
     * @Template
     */
    public function toggle(TwitterUser $user, Tag $tag, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $userTag = $user->hasTag($tag);

        if($userTag) {

            $em->remove($userTag);
            $toggle = 0;

        }
        else {

            $userTag = new UserTag();
            $userTag->setUser($user);
            $userTag->setTag($tag);
            $em->persist($userTag);
            $toggle = 1;

        }

        $em->flush();

        return new JsonResponse(["status" => "ok", "toggle" => $toggle]);


    }

}