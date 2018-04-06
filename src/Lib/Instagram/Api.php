<?php

namespace Maalls\SocialMediaContentBundle\Lib\Instagram;

class Api {

    private $access_token;
    private $logger;

    public function __construct($access_token)
    {

        $this->access_token = $access_token;

    }


    public function request($action, $parameters = [], $method = "GET")
    {

        $action = trim($action, "/");
        $parameters["access_token"] = $this->access_token;
        $query = http_build_query($parameters);
        $url = "https://api.instagram.com/v1/$action?" . $query;

        $this->log("Calling $url");
        $ch = curl_init($url);

        curl_setopt_array($ch, [

            CURLOPT_RETURNTRANSFER => true

        ]);

        $rsp = curl_exec($ch);

        if(curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {

            throw new \Exception("Error " . $rsp);

        }


        return $rsp;

    }

    public function setLogger($logger)
    {

        $this->logger = $logger;

    }

    public function log($msg, $level = "info")
    {

        if($this->logger) {
        
            $this->logger->log($msg, $level);

        }

    }

}