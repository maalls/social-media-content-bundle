<?php

namespace Maalls\SocialMediaContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
/**
 * @ORM\Entity(repositoryClass="Maalls\SocialMediaContentBundle\Repository\TwitterTrendRepository")
 * @ORM\Table(
 *
 *   uniqueConstraints={
 *        @UniqueConstraint(name="woeid_datetime_name_unique", 
 *            columns={"woeid", "datetime", "name"})
 *    }
 *
 * )
 *
 */
class TwitterTrend
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    // add your own fields

    /**
    * @ORM\Column(type="string", length=32)
    */
    private $woeid;

    /**
    * @ORM\Column(type="string", length=64)
    */
    private $name = '';

    /**
    * @ORM\Column(type="string", length=64, nullable=true)
    */
    private $promoted_content = null;

    /**
    * @ORM\Column(type="string", length=64)
    */
    private $tweet_volume;

    /**
    * @ORM\Column(type="datetime")
    */
    protected $datetime;

    /**
    * @ORM\Column(type="integer")
    */
    private $rank;


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
    public function getWoeid()
    {
        return $this->woeid;
    }

    /**
     * @param mixed $woeid
     *
     * @return self
     */
    public function setWoeid($woeid)
    {
        $this->woeid = $woeid;

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
    public function getPromotedContent()
    {
        return $this->promoted_content;
    }

    /**
     * @param mixed $promoted_content
     *
     * @return self
     */
    public function setPromotedContent($promoted_content)
    {
        $this->promoted_content = $promoted_content;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTweetVolume()
    {
        return $this->tweet_volume;
    }

    /**
     * @param mixed $tweet_volume
     *
     * @return self
     */
    public function setTweetVolume($tweet_volume)
    {
        $this->tweet_volume = $tweet_volume;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param mixed $datetime
     *
     * @return self
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @param mixed $rank
     *
     * @return self
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }
}
