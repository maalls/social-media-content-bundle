<?php

namespace Maalls\SocialMediaContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Maalls\SocialMediaContentBundle\Repository\TwitterUserRepository")
 * @ORM\Table(indexes={@ORM\Index(name="screen_name", columns={"screen_name"})})

 */
class TwitterUser
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="bigint", options={"unsigned"=true})
     */
    private $id;

    // add your own fields


    /**
    * @ORM\Column(type="string", length=64)
    */
    private $name;

    /**
    * @ORM\Column(type="string", length=64)
    */
    private $screen_name;

    /**
    * @ORM\Column(type="text", nullable=true)
    */
    private $description;

    /**
    * @ORM\Column(type="string", length=8, nullable=true)
    */
    private $lang;

    /**
    * @ORM\Column(type="string", length=256, nullable=true)
    */
    protected $location;


    /**
    * @ORM\Column(type="boolean")
    */
    private $verified;

    /**
    * @ORM\Column(type="boolean")
    */
    private $protected;

    /**
     * @ORM\Column(type="bigint", options={"unsigned"=true})
     */
    private $followers_count;

    /**
     * @ORM\Column(type="bigint", options={"unsigned"=true})
     */
    private $friends_count;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $listed_count;

     /**
    * @ORM\Column(type="datetime")
    */
    private $updated_at;

    /**
    * @ORM\Column(type="datetime", nullable=true)
    */
    private $timeline_updated_at;

    /**
     * @ORM\OneToMany(targetEntity="Maalls\SocialMediaContentBundle\Entity\Tweet", mappedBy="user")
     */
    private $tweets;



    public function __construct() {
    
        $this->tweets = new \Doctrine\Common\Collections\ArrayCollection();
    
    }

    public function getTweets()
    {

        return $this->tweets;

    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getScreenName()
    {
        return $this->screen_name;
    }

    /**
     * @param mixed $screen_name
     *
     * @return self
     */
    public function setScreenName($screen_name)
    {
        $this->screen_name = $screen_name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     *
     * @return self
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     *
     * @return self
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVerified()
    {
        return $this->verified;
    }

    /**
     * @param mixed $verified
     *
     * @return self
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProtected()
    {
        return $this->protected;
    }

    /**
     * @param mixed $protected
     *
     * @return self
     */
    public function setProtected($protected)
    {
        $this->protected = $protected;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFollowersCount()
    {
        return $this->followers_count;
    }

    /**
     * @param mixed $followers_count
     *
     * @return self
     */
    public function setFollowersCount($followers_count)
    {
        $this->followers_count = $followers_count;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFriendsCount()
    {
        return $this->friends_count;
    }

    /**
     * @param mixed $friends_count
     *
     * @return self
     */
    public function setFriendsCount($friends_count)
    {
        $this->friends_count = $friends_count;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getListedCount()
    {
        return $this->listed_count;
    }

    /**
     * @param mixed $listed_count
     *
     * @return self
     */
    public function setListedCount($listed_count)
    {
        $this->listed_count = $listed_count;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     *
     * @return self
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimelineUpdatedAt()
    {
        return $this->timeline_updated_at;
    }

    /**
     * @param mixed $timeline_updated_at
     *
     * @return self
     */
    public function setTimelineUpdatedAt($timeline_updated_at)
    {
        $this->timeline_updated_at = $timeline_updated_at;

        return $this;
    }
}
