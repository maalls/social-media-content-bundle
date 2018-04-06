<?php

namespace Maalls\SocialMediaContentBundle\Repository;

use Maalls\SocialMediaContentBundle\Entity\TwitterUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TwitterUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method TwitterUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method TwitterUser[]    findAll()
 * @method TwitterUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TwitterUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TwitterUser::class);
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
