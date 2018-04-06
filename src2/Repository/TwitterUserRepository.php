<?php

namespace Maalls\SocialMediaContentBundle\Repository;

use Maalls\SocialMediaContentBundle\Entity\TwitterUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Api;
/**
 * @method TwitterUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method TwitterUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method TwitterUser[]    findAll()
 * @method TwitterUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TwitterUserRepository extends ServiceEntityRepository
{

    protected $api;

    public function __construct(RegistryInterface $registry, Api $api)
    {
        $this->api = $api;
        parent::__construct($registry, TwitterUser::class);
    }


    public function generateFromScreenName($screen_name)
    {

        $user = $this->findOneBy(["screen_name" => $screen_name]);

        if(!$user) {

            $rsp = $this->api->get("users/lookup", ["screen_name" => $screen_name]);

            if(isset($rsp->errors) && $rsp->errors[0]->code == 17) {

                $user = false;

            }
            else {

                $profile = $rsp[0];
                $user = $this->generateFromJson($profile);

            }
        }

        return $user;
        
    }


    public function generateFromJson($profile)
    {

        $user = $this->find($profile->id_str);

        if(!$user) {
   
            $user = new TwitterUser();

        }

        $this->hydrateJson($user, $profile);

        $this->getEntityManager()->persist($user);

        return $user;

    }

    public function hydrateJson(TwitterUser $user, $profile)
    {

        $user->setId($profile->id_str);
        $user->setUpdatedAt(new \Datetime());
        $user->setScreenName($profile->screen_name);
        $user->setName($profile->name);
        $user->setDescription($profile->description);
        $user->setProtected($profile->protected);
        $user->setFollowersCount($profile->followers_count);
        $user->setFriendsCount($profile->friends_count);
        $user->setListedCount($profile->listed_count);
        $user->setVerified($profile->verified);
        $user->setLang($profile->lang);
        
        $user->setLocation($profile->location);

        return $user;

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
