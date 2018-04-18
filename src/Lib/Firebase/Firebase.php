<?php

namespace Maalls\SocialMediaContentBundle\Lib\Firebase;

class Firebase {

    protected $key;

    protected $url;

    public function __construct($url, $key)
    {

        $this->url = $url;
        $this->key = $key;

    }

    public function getUrl()
    {

        return $this->url;

    }

    public function getKey()
    {

        return $this->key;

    }

    public function set($path, $data)
    {


        $json = json_encode($data);

        $url = $this->url . "/" . trim($path, "/") . ".json";

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_RETURNTRANSFER => true
        ]);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

}