<?php

namespace Maalls\SocialMediaContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Maalls\SocialMediaContentBundle\Repository\TweetRepository")
 */
class Search
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
    * @ORM\Column(type="string", length=16)
    */
    private $type;

    /**
    * @ORM\Column(type="text")
    */
    private $query;


    /**
    * @ORM\Column(type="string", length=64, nullable=true) 
    */
    private $since_id;

    /**
    * @ORM\Column(type="string", length=64, nullable=true)
    */
    private $max_id;

    /**
    * @ORM\Column(type="string", length=64, nullable=true) 
    */
    private $greatest_id;

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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     *
     * @return self
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSinceId()
    {
        return $this->since_id;
    }

    /**
     * @param mixed $since_id
     *
     * @return self
     */
    public function setSinceId($since_id)
    {
        $this->since_id = $since_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxId()
    {
        return $this->max_id;
    }

    /**
     * @param mixed $max_id
     *
     * @return self
     */
    public function setMaxId($max_id)
    {
        $this->max_id = $max_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGreatestId()
    {
        return $this->greatest_id;
    }

    /**
     * @param mixed $greatest_id
     *
     * @return self
     */
    public function setGreatestId($greatest_id)
    {
        $this->greatest_id = $greatest_id;

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