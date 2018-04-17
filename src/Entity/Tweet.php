<?php

namespace Maalls\SocialMediaContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Maalls\SocialMediaContentBundle\Repository\TweetRepository")
 */
class Tweet
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="bigint", options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="TwitterUser", inversedBy="tweets")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $user;

    /**
    * @ORM\Column(type="text")
    */
    protected $text = '';

    /**
    * @ORM\Column(type="string", length=6, nullable=true)
    */
    protected $lang = '';

    /**
    * @ORM\Column(type="datetime")
    */
    protected $posted_at;

    /**
     * @ORM\Column(type="bigint", options={"unsigned"=true}, nullable=true)
     */
    protected $in_reply_to_status_id;

    /**
     * @ORM\ManyToOne(targetEntity="Tweet")
     * @ORM\JoinColumn(name="retweet_status_id", referencedColumnName="id", onDelete="SET NULL")
     **/
    protected $retweet_status;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_quote_status = false;

    /**
    * @ORM\Column(type="integer", options={"unsigned"=true})
    */
    protected $retweet_count = 0;

    /**
    * @ORM\Column(type="integer", options={"unsigned"=true})
    */
    protected $favorite_count = 0;

    /**
    * @ORM\Column(type="integer", options={"unsigned"=true})
    */
    protected $reply_count = 0;

    /**
    * @ORM\Column(type="datetime")
    */
    protected $stats_updated_at;


    public function getHtml()
    {

        return preg_replace(
            '#((https?|ftp)://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)#i',
            '<a href="$1" target="_blank">$3</a>$4', $this->getText());

    }

    public function isReply()
    {

        return $this->text[0] == "@";

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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     *
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     *
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostedAt()
    {
        return $this->posted_at;
    }

    /**
     * @param mixed $posted_at
     *
     * @return self
     */
    public function setPostedAt($posted_at)
    {
        $this->posted_at = $posted_at;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInReplyToStatusId()
    {
        return $this->in_reply_to_status_id;
    }

    /**
     * @param mixed $in_reply_to_status_id
     *
     * @return self
     */
    public function setInReplyToStatusId($in_reply_to_status_id)
    {
        $this->in_reply_to_status_id = $in_reply_to_status_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRetweetStatus()
    {
        return $this->retweet_status;
    }

    /**
     * @param mixed $retweet_status
     *
     * @return self
     */
    public function setRetweetStatus($retweet_status)
    {
        $this->retweet_status = $retweet_status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsQuoteStatus()
    {
        return $this->is_quote_status;
    }

    /**
     * @param mixed $is_quote_status
     *
     * @return self
     */
    public function setIsQuoteStatus($is_quote_status)
    {
        $this->is_quote_status = $is_quote_status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRetweetCount()
    {
        return $this->retweet_count;
    }

    /**
     * @param mixed $retweet_count
     *
     * @return self
     */
    public function setRetweetCount($retweet_count)
    {
        $this->retweet_count = $retweet_count;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFavoriteCount()
    {
        return $this->favorite_count;
    }

    /**
     * @param mixed $favorite_count
     *
     * @return self
     */
    public function setFavoriteCount($favorite_count)
    {
        $this->favorite_count = $favorite_count;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReplyCount()
    {
        return $this->reply_count;
    }

    /**
     * @param mixed $reply_count
     *
     * @return self
     */
    public function setReplyCount($reply_count)
    {
        $this->reply_count = $reply_count;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatsUpdatedAt()
    {
        return $this->stats_updated_at;
    }

    /**
     * @param mixed $stats_updated_at
     *
     * @return self
     */
    public function setStatsUpdatedAt($stats_updated_at)
    {
        $this->stats_updated_at = $stats_updated_at;

        return $this;
    }
}
