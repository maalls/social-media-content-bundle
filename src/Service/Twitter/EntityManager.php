<?php


namespace Maalls\SocialMediaContentBundle\Service\Twitter;


class EntityManager {


    public function __construct(\Doctrine\Common\Persistence\ObjectManager $em, \Maalls\SocialMediaContentBundle\Lib\Twitter\Api $api)
    {

        $this->conn = $em->getConnection();
        $this->api = $api;

    }


    

}
