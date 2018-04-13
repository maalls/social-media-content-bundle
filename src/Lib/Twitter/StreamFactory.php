<?php
namespace Maalls\SocialMediaContentBundle\Lib\Twitter;

class StreamFactory {
    

    public function __construct(
        \Doctrine\Common\Persistence\ObjectManager $em, 
        Credential $credential, 
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

        var_dump($this->track);
        if(!$this->track) {

            throw new \Exception("Nothing to track.");

        }

        $stream = new Stream($this->credential->accessToken, $this->credential->accessTokenSecret, \Phirehose::METHOD_FILTER);
        $stream->setEntityManager($this->em);
        $stream->setCounter($this->counter);
        $stream->consumerKey = $this->credential->consumerKey;
        $stream->consumerSecret = $this->credential->consumerSecret;

        $stream->setFirebase($this->firebaseFactory->create());

        $stream->setTrack($this->track);

        return $stream;

    }

}