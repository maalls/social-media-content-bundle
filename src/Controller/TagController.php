<?php

namespace Maalls\SocialMediaContentBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Maalls\SocialMediaContentBundle\Entity\Tag;
use Maalls\SocialMediaContentBundle\Form\TagType;

/**
 * @Route("/tags")
 */
class TagController extends Controller
{

    /**
     * @Route("/", name="tags")
     * @Template
     */
    public function index(Request $request)
    {
        $qb = $this->getDoctrine()
            ->getManager()
            ->getRepository(Tag::class)
            ->createQueryBuilder("t");

        $query = $qb->getQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1),
            200
        );

        return ['pagination' => $pagination];

    }

    /**
     * @Route("/show/{id}", name="tags_show")
     * @Template
     */
    public function show($id, Request $request)
    {

        $tag = $this->getDoctrine()->getManager()->getRepository(Tag::class)->find($id);

        return ["tag" => $tag];

    }

    /**
     * @Route("/create", name="tags_create")
     * @Template
     */
    public function create(Request $request)
    {

        // just setup a fresh $task object (remove the dummy data)
        $task = new Tag();

        $form =  $form = $this->createForm(TagType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $tag = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            return $this->redirectToRoute('tags');
        }

        return ['form' => $form->createView()];

    }

}
