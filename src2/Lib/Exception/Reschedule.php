<?php

namespace Maalls\SocialMediaContentBundle\Lib\Exception;

class Reschedule extends \Exception {

    private $scheduled_at;

    public function __construct(string $messge = "",int $code = 0, $scheduled_at, Throwable $previous = NULL)
    {

        parent::__construct($message, $code, $previous);

        $this->scheduled_at = $scheduled_at;

    }

    public function getScheduledAt()
    {

        return $this->scheduled_at;

    }

}