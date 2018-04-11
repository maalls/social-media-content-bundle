<?php

namespace Maalls\SocialMediaContentBundle\Lib\Twitter;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Credential;

class Api extends \Maalls\SocialMediaContentBundle\Lib\Loggable {

    private $credentials;
    private $nextIndex = 0;
    private $cacheLocation = '';
    private $apiDatetime = null;
    
    public function __construct($twitter_credentials_file, $twitter_api_cache_folder = '') {

        $credentialArray = json_decode(file_get_contents($twitter_credentials_file));
        
        $this->credentials = [];

        foreach($credentialArray as $array) {

            $this->credentials[] = new Credential(...$array);

        }

        $this->setCacheLocation($twitter_api_cache_folder);

    }

    public function get($action, $parameters = [], $use_cache = true)
    {

        if($this->cacheLocation) {
            
            $cache_file = $this->getCacheFilename($action, $parameters);

            if(file_exists($cache_file) && $use_cache) {

                $this->log("Cached API.");
                $rsp = json_decode(file_get_contents($cache_file));

            }
            else {

                //echo "Calling API." . PHP_EOL;
                $this->log("API call.");
                $rsp = $this->getFromApi($action, $parameters);

                if(!isset($rsp->errors)) {

                    file_put_contents($cache_file, json_encode($rsp));
                }

            }

            $this->apiDatetime = $this->getCachedDatetime($action, $parameters);

        }
        else {

            $rsp = $this->getFromApi($action, $parameters);
            $this->apiDatetime = new \Datetime();

        }

        return $rsp;

    }

    public function getFromApi($action, $parameters = []) 
    {

        $retry = 5;

        for($i = 0; $i < $retry; $i++) {

            try {

                $api = $this->create();
                return $api->get($action, $parameters);


            }
            catch(\Abraham\TwitterOAuth\TwitterOAuthException $e) {

                $this->log("Connection timeout, retrying in 5sec.");
                sleep(5);
                $retry--;

            }
            catch(\Exception $e) {

            
                $this->log("Error " . get_class($e) . " : " . $e->getMessage(). " (" . $e->getCode() . ") retrying in few sec.");
                sleep(5);
                $retry--;

            

            }

        }

        throw $e;

    }

    public function getApiDatetime()
    {

        return $this->apiDatetime;

    }   

    public function getCachedDatetime($action, $parameters = [])
    {

        if($this->cacheLocation) {

            $cache_file = $this->getCacheFilename($action, $parameters);

            if(file_exists($cache_file)) {

                $time = filemtime($cache_file);

                return new \Datetime(date("Y-m-d H:i:s", $time));

            }
            else {

                return null;

            }

        }
        else {

            return false;

        }

    }

    public function getCacheFilename($action, $parameters = [])
    {

        return $this->cacheLocation . "/" . sha1($action . "?" . http_build_query($parameters)) . ".json";

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

    public function getCacheLocation()
    {

        return $this->cacheLocation;

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