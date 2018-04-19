<?php
namespace Maalls\SocialMediaContentBundle\Lib\Twitter;

class StreamFactory {
    

    public function __construct(
        \Doctrine\Common\Persistence\ObjectManager $em, 
        Credential $credential, 
        \Maalls\SocialMediaContentBundle\Service\Firebase\FirebaseCounter $counter)
    {

        $this->credential = $credential;
        $this->em = $em;
        $this->counter = $counter;

    }


    public function createStream()
    {

        $stream = new Stream($this->credential->accessToken, $this->credential->accessTokenSecret, \Phirehose::METHOD_FILTER);
        $stream->setEntityManager($this->em);
        $stream->setCounter($this->counter);
        $stream->consumerKey = $this->credential->consumerKey;
        $stream->consumerSecret = $this->credential->consumerSecret;
        
        return $stream;

    }

}