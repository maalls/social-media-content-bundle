<?php

namespace Maalls\SocialMediaContentBundle\Lib\Twitter;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Credential;

class Api {

    private $credentials;
    private $nextIndex = 0;
    private $cacheLocation = '';
    
    public function __construct($twitter_credentials_file, $twitter_api_cache_folder = '') {

        $credentialArray = json_decode(file_get_contents($twitter_credentials_file));
        
        $this->credentials = [];

        foreach($credentialArray as $array) {

            $this->credentials[] = new Credential(...$array);

        }

        $this->setCacheLocation($twitter_api_cache_folder);

    }

    public function get($action, $parameters = [])
    {

        if($this->cacheLocation) {
            
            $cache_file = $this->cacheLocation . "/" . sha1($action . "?" . http_build_query($parameters)) . ".json";

            if(file_exists($cache_file)) {

                $rsp = json_decode(file_get_contents($cache_file));

            }
            else {

                //echo "Calling API." . PHP_EOL;
                $api = $this->create();
                $rsp = $api->get($action, $parameters);

                if(!isset($rsp->errors)) {

                    file_put_contents($cache_file, json_encode($rsp));

                }

            }
        }
        else {

            $api = $this->create();
            $rsp = $api->get($action, $parameters);

        }

        return $rsp;

    }

    public function verifyCredentials()
    {

        foreach($this->credentials as $credential) {

            $connection = $this->createConnection($credential);

            $rsp = $connection->get("account/verify_credentials", [], false);

            if(isset($rsp->id_str)) {


            }
            else {

                throw new \Exception("Invalid token " . $credential->accessToken);

            }

        }

    }

    public function create()
    {

        $credential = $this->credentials[$this->nextIndex];

        $connection = $this->createConnection($credential);
        
        $this->nextIndex = ($this->nextIndex + 1) % count($this->credentials);
        
        return $connection;

    }

    public function createConnection($credential)
    {

        $connection = new \Abraham\TwitterOAuth\TwitterOAuth($credential->consumerKey, $credential->consumerSecret, $credential->accessToken, $credential->accessTokenSecret);
        
        return $connection;

    }

    public function setCacheLocation($cacheLocation)
    {

        if($cacheLocation && !file_exists($cacheLocation)) {

            $ok = mkdir($cacheLocation);

            if(!$ok) {

                throw new \Exception("Unable to create $cacheLocation");

            }

        }

        $this->cacheLocation = $cacheLocation;

    }

}