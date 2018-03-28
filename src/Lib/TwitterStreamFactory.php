<?php
namespace Maalls\SocialMediaContentBundle\Lib;

class TwitterStreamFactory {
    

    public function __construct(\Doctrine\Common\Persistence\ObjectManager $em, TwitterCredential $credential)
    {

        $this->credential = $credential;

        $this->em = $em;

    }


    public function createStream()
    {

        $stream = new TwitterStream($this->credential->access_token, $this->credential->access_token_secret, \Phirehose::METHOD_FILTER);
        $stream->setEntityManager($this->em);
        $stream->consumerKey = $this->credential->consumer_key;
        $stream->consumerSecret = $this->credential->consumer_secret;

        return $stream;

    }

}