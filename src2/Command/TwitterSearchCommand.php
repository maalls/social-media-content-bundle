<?php

namespace Maalls\SocialMediaContentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Maalls\SocialMediaContentBundle\Lib\LoggerOutputAdapter;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Search as TwitterSearch;

class TwitterSearchCommand extends Command
{

    public function __construct(TwitterSearch $search)
    {

        parent::__construct();
        $this->search = $search;

    }

    protected function configure()
    {
        $this
        ->setName('smc:twitter:search')
        ->setDescription('Twitter search tweets.')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Starting search");
        $this->search->setLogger(new LoggerOutputAdapter($output));
        $this->search->start();

    }

}