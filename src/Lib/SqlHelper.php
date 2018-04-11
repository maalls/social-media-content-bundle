<?php

namespace Maalls\SocialMediaContentBundle\Lib;

class SqlHelper {


    public static function insert($conn, $table, $fields, $parameters, $onDuplicateKey = '') {

        $entryCount = count($parameters) / count($fields);

        if($entryCount != round(count($parameters) / count($fields))) {

            throw new \Exception("Number of Fields and parameters not compatible.");

        }

        $values = trim(str_repeat("?,", count($fields)), ",");
        $values = trim(str_repeat("($values),", $entryCount), ",");

        $onDuplicateKey = $onDuplicateKey ? " ON DUPLICATE KEY UPDATE $onDuplicateKey" : '';

        $query = "insert into $table (`" . implode("`, `", $fields) . "`) values " . $values . " $onDuplicateKey";

        $stmt = $conn->prepare($query);
        $stmt->execute($parameters);

        return $stmt;

    }

}