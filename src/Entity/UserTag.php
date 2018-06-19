<?php

namespace Maalls\SocialMediaContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Maalls\SocialMediaContentBundle\Repository\UserTagRepository")
 *
 */
class UserTag
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="TwitterUser", inversedBy="userTags")
     * @ORM\JoinColumn(referencedColumnName="id")
     **/
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Tag", inversedBy="taggedUsers")
     * @ORM\JoinColumn(referencedColumnName="id")
     **/
    private $tag;


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
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     *
     * @return self
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }
}