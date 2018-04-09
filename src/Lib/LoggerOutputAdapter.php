<?php 

namespace Maalls\SocialMediaContentBundle\Lib;


class LoggerOutputAdapter {

    protected $output = null;

    public function __construct($output) {

        $this->output = $output;

    }
    public function log($msg, $level = 'info') {

        $this->output->writeln(date("Y-m-d H:i:s") . " [$level] $msg");

    }

}