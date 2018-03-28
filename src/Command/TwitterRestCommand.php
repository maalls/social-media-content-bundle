<?php

namespace Maalls\SocialMediaContentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Maalls\SocialMediaContentBundle\Lib\LoggerOutputAdapter;
use Maalls\SocialMediaContentBundle\Lib\TwitterStreamFactory;
class TwitterRestCommand extends Command
{

    public function __construct(\Abraham\TwitterOAuth\TwitterOAuth $api)
    {

        parent::__construct();
        $this->api = $api;

    }

    protected function configure()
    {
        $this
        ->setName('smc:twitter-rest')
        ->setDescription('Twitter rest tweets.')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $rsp = $this->api->get("search/tweets", ["q" => "hello"]);

    }

}