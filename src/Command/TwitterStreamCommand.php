<?php

namespace Maalls\SocialMediaContentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Maalls\SocialMediaContentBundle\Lib\LoggerOutputAdapter;
use Maalls\SocialMediaContentBundle\Lib\Twitter\StreamFactory;
class TwitterStreamCommand extends Command
{

    public function __construct(StreamFactory $factory)
    {

        parent::__construct();
        $this->stream = $factory->createStream();

    }

    protected function configure()
    {
        $this
        ->setName('smc:twitter:stream')
        //->addArgument('track', InputArgument::REQUIRED, 'A list keywords to track.')
        ->setDescription('Watch tweets.')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->stream->setLogger(new LoggerOutputAdapter($output));
        $this->stream->consume();


    }

}