<?php 

namespace Maalls\SocialMediaContentBundle\Lib;

class TwitterCredential {


    public $consumer_key;
    public $consumer_secret;

    public $access_token;

    public $access_token_secret;


    public function __construct($consumer_key, $consumer_secret, $access_token, $access_token_secret)
    {

        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->access_token = $access_token;
        $this->access_token_secret = $access_token_secret;

    }

}