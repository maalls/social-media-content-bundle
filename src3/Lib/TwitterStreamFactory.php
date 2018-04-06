<?php
namespace Maalls\SocialMediaContentBundle\Lib;

class TwitterStreamFactory {
    

    public function __construct(
        \Doctrine\Common\Persistence\ObjectManager $em, 
        TwitterCredential $credential, 
        \Maalls\SocialMediaContentBundle\Lib\Firebase\Factory $firebaseFactory, 
        \Maalls\SocialMediaContentBundle\Lib\Counter $counter,
        $track)
    {

        $this->credential = $credential;

        $this->em = $em;

        $this->firebaseFactory = $firebaseFactory;

        $this->counter = $counter;

        $this->track = explode(",", $track);

    }


    public function createStream()
    {

        $stream = new TwitterStream($this->credential->access_token, $this->credential->access_token_secret, \Phirehose::METHOD_FILTER);
        $stream->setEntityManager($this->em);
        $stream->setCounter($this->counter);
        $stream->consumerKey = $this->credential->consumer_key;
        $stream->consumerSecret = $this->credential->consumer_secret;

        $stream->setFirebase($this->firebaseFactory->create());

        $stream->setTrack($this->track);

        return $stream;

    }

}