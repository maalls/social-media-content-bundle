<?php

namespace Maalls\SocialMediaContentBundle\Repository;

use Maalls\SocialMediaContentBundle\Entity\TwitterUser;
use Maalls\SocialMediaContentBundle\Entity\Tweet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Api;
use Maalls\SocialMediaContentBundle\Repository\LoggableServiceEntityRepository;
/**
 * @method TwitterUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method TwitterUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method TwitterUser[]    findAll()
 * @method TwitterUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TwitterUserRepository extends LoggableServiceEntityRepository
{

    protected $api;

    public function __construct(RegistryInterface $registry, Api $api)
    {
        $this->api = $api;
        parent::__construct($registry, TwitterUser::class);
    }


    public function updateTimeline($user)
    {

        $em = $this->getEntityManager();
        $tweetRep = $em->getRepository(Tweet::class);

        $timeline = $this->api->get("statuses/user_timeline", ["user_id" => $user->getId(), "count" => 200]);
        $dataDatetime = $this->api->getApiDatetime();

        if(!isset($timeline->errors) && !isset($timeline->error)) {

                $stats = [];

                foreach($timeline as $l => $t) {

                    $tweet = $tweetRep->generateFromJson($t, $dataDatetime);
                    
                }

                $user->setTimelineUpdatedAt($dataDatetime);
                $em->persist($user);
                $em->flush();

        }
        else {

            if(isset($timeline->errors)) {

                throw new \Exception($timeline->errors[0]->message, $timeline->errors[0]->code);

            }
            else {

                throw new \Exception("Unexpected error : " . json_encode($timeline));

            }

        }



    }

    public function generateFromScreenName($screen_name)
    {

        $user = $this->findOneBy(["screen_name" => $screen_name]);

        if(!$user) {

            $rsp = $this->api->get("users/lookup", ["screen_name" => $screen_name]);
            $dataDatetime = $this->api->getApiDatetime();

            if(isset($rsp->errors)) {

                if($rsp->errors[0]->code == 17) {

                    $user = false;

                }
                else {

                    throw new \Exception($rsp->errors[0]->message, $rsp->errors[1]->code);

                }

            }
            else {

                $profile = $rsp[0];
                $user = $this->generateFromJson($profile, $dataDatetime);
                $em = $this->getEntityManager();
                $em->persist($user);
                $em->flush();


            }
        }

        return $user;
        
    }


    public function generateFromJson($profile, $dataDatetime)
    {

        $user = $this->find($profile->id_str);

        if(!$user) {
   
            $user = new TwitterUser();

        }

        $this->hydrateJson($user, $profile, $dataDatetime);

        $this->getEntityManager()->persist($user);

        return $user;

    }

    public function hydrateJson(TwitterUser $user, $profile, $dataDatetime)
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
        $user->setProfileUpdatedAt($dataDatetime);
        
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
