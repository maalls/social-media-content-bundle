<?php

namespace Maalls\SocialMediaContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
/**
 * @ORM\Entity(repositoryClass="Maalls\SocialMediaContentBundle\Repository\TwitterUserFollowerRepository")
 * @ORM\Table(
 *
 *   uniqueConstraints={
 *        @UniqueConstraint(name="user_follower_unique", 
 *            columns={"twitter_user_id", "follower_id"})
 *    }
 *
 * )
 */
class TwitterUserFollower
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="TwitterUser", inversedBy="followers")
     * @ORM\JoinColumn(referencedColumnName="id")
     **/
    private $twitterUser;


    /**
     * @ORM\ManyToOne(targetEntity="TwitterUser", inversedBy="friends")
     * @ORM\JoinColumn(referencedColumnName="id")
     **/
    private $follower;


    /**
    * @ORM\Column(type="datetime")
    */
    private $created_at;

    /**
    * @ORM\Column(type="datetime")
    */
    private $updated_at;

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
    public function getTwitterUser()
    {
        return $this->twitterUser;
    }

    /**
     * @param mixed $twitterUser
     *
     * @return self
     */
    public function setTwitterUser($twitterUser)
    {
        $this->twitterUser = $twitterUser;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFollower()
    {
        return $this->follower;
    }

    /**
     * @param mixed $follower
     *
     * @return self
     */
    public function setFollower($follower)
    {
        $this->follower = $follower;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     *
     * @return self
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

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
}
