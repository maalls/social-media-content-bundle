<?php

namespace Maalls\SocialMediaContentBundle\Lib;

class Woeid {


    public function getLocations() {

        return [
            ["woeid" => "1110809", "name" => "Japan"],
            ["woeid" => "1118370", "name" => "Tokyo"],
            ["woeid" => "15015370", "name" => "Osaka"]
        ];

    }

    public function findLocation($woeid)
    {

        foreach($this->getLocations() as $location) {

            if($location["woeid"] == $woeid) {

                return $location;

            }

        }

        return false;

    }

}