<?php

namespace Maalls\SocialMediaContentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Maalls\SocialMediaContentBundle\Lib\LoggerOutputAdapter;
use Maalls\SocialMediaContentBundle\Lib\TwitterStreamFactory;
class TwitterStreamCommand extends Command
{

    public function __construct(TwitterStreamFactory $factory)
    {

        parent::__construct();
        $this->stream = $factory->createStream();

    }

    protected function configure()
    {
        $this
        ->setName('smc:twitter-stream')
        ->setDescription('Watch tweets.')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

            $this->stream->setLog(new LoggerOutputAdapter($output));
            $this->stream->setTrack(["apple"]);
            $this->stream->consume();


    }

}