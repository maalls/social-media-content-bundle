<?php 

namespace Maalls\SocialMediaContentBundle\Lib\Instagram;


class Factory {



    public function __construct($key, $secret, $callback, $accessToken)
    {


        $this->key = $key;
        $this->secret = $secret;
        $this->callback = $callback;
        $this->accessToken = $accessToken;


    }


    public function createLoginApi()
    {

        $api = new \MetzWeb\Instagram\Instagram(["apiKey" => $this->key, "apiSecret" => $this->secret, "apiCallback" => $this->callback]);
        return $api;

    }

    public function createApi()
    {

        $api = new Api($this->accessToken);

        return $api;

    }

}