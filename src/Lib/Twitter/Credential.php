<?php 

namespace Maalls\SocialMediaContentBundle\Lib\Twitter;

class Credential {


    public $consumerKey;
    public $consumerSecret;

    public $accessToken;

    public $accessTokenSecret;


    public function __construct($consumer_key, $consumer_secret, $access_token, $access_token_secret)
    {

        $this->consumerKey = $consumer_key;
        $this->consumerSecret = $consumer_secret;
        $this->accessToken = $access_token;
        $this->accessTokenSecret = $access_token_secret;

    }

}