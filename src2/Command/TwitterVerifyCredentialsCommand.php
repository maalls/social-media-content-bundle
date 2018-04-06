<?php

namespace Maalls\SocialMediaContentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Maalls\SocialMediaContentBundle\Lib\LoggerOutputAdapter;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Api;
class TwitterVerifyCredentialsCommand extends Command
{

    public function __construct(Api $api)
    {

        parent::__construct();
        $this->api = $api;

    }

    protected function configure()
    {
        $this
        ->setName('smc:twitter:api:verify-credentials')
        //->addArgument('track', InputArgument::REQUIRED, 'A list keywords to track.')
        ->setDescription('Check twitter api credentials.')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->api->verifyCredentials();


    }

}