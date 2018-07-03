<?php

namespace Maalls\SocialMediaContentBundle\Lib\Twitter;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Credential;

class Api extends \Maalls\SocialMediaContentBundle\Lib\Loggable {

    private $credentials;
    private $nextIndex = 0;
    private $cacheLocation = '';
    private $apiDatetime = null;
    private $cacheDuration = null;
    
    public function __construct($twitter_credentials_file = '', $twitter_api_cache_folder = '') {

        if($twitter_credentials_file) {
        
            $credentialArray = json_decode(file_get_contents($twitter_credentials_file));
            
            $this->credentials = [];

            foreach($credentialArray as $array) {

                $this->credentials[] = new Credential(...$array);

            }
        }

        if($twitter_api_cache_folder) {
        
            $this->setCacheLocation($twitter_api_cache_folder);

        }

    }

    public function get($action, $parameters = [], $force_cache = true)
    {

        if($this->cacheLocation) {
            
            $cache_file = $this->getCacheFilename($action, $parameters);

            $use_cache = false;

            if(file_exists($cache_file)) {

                $updated_at = filemtime($cache_file);
                if($force_cache) {

                    $this->log("Force cache");
                    $use_cache = true;

                }
                elseif($this->cacheDuration === null) {

                    $this->log("Cache duration null");
                    $use_cache = true;

                }
                elseif(($updated_at + $this->cacheDuration) > time()) {

                    $this->log("Cache unexpired.");
                    $use_cache = true;

                }
            }

            if($use_cache) {

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
        $sleep = 5;

        for($i = 0; $i < $retry; $i++) {

            try {

                $api = $this->create();
                $rsp = $api->get($action, $parameters);

                if(isset($rsp->errors)) {

                    $error = $rsp->errors[0];

                    switch($error->code) {


                        case 130:
                        case 131:
                            sleep(10 * (6 - $retry));
                            $retry--;
                            break;
                        default:
                            return $rsp;
                    }

                }
                else {

                    return $rsp;

                }


            }
            catch(\Abraham\TwitterOAuth\TwitterOAuthException $e) {

                $this->log("Connection timeout, retrying in 5sec.");
                sleep($sleep);


            }
            catch(\Exception $e) {

            
                $this->log("Error " . get_class($e) . " : " . $e->getMessage(). " (" . $e->getCode() . ") retrying in few sec.");
                sleep($sleep);

            

            }

            $sleep = 2*$sleep;

        }

        throw new \Exception("Unable to query Twitter.");

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

        if(!$this->credentials) {

            throw new \Exception("Credentials required.");

        }

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

    public function setCacheDuration($duration)
    {

        $this->cacheDuration = $duration;

    }

    public function setCacheLocation($cacheLocation)
    {

        if($cacheLocation && !file_exists($cacheLocation)) {

            $ok = mkdir($cacheLocation, 0777, true);

            if(!$ok) {

                throw new \Exception("Unable to create $cacheLocation");

            }

        }

        $this->cacheLocation = $cacheLocation;

    }

}