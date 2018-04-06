<?php

namespace Maalls\SocialMediaContentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Maalls\SocialMediaContentBundle\Lib\LoggerOutputAdapter;
use Maalls\SocialMediaContentBundle\Lib\TwitterSearch;
class TwitterRestCommand extends Command
{

    public function __construct(\Abraham\TwitterOAuth\TwitterOAuth $rest)
    {

        parent::__construct();
        $this->rest = $rest;

    }

    protected function configure()
    {
        $this
        ->setName('smc:twitter:rest')
        ->setDescription('Twitter rest tweets.')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $rsp = $this->rest->get("search/tweets", ["q" => "hello"]);

        echo json_encode($rsp) . PHP_EOL;

    }

}