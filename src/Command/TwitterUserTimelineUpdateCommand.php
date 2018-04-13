<?php

namespace Maalls\SocialMediaContentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Maalls\SocialMediaContentBundle\Lib\LoggerOutputAdapter;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Search;
use Maalls\SocialMediaContentBundle\Entity\TwitterUser;


class TwitterUserTimelineUpdateCommand extends Command
{

    public function __construct(\Doctrine\Common\Persistence\ObjectManager $em)
    {

        parent::__construct();
        $this->em = $em;

    }

    protected function configure()
    {
        $this
        ->setName('smc:twitter:user:timeline:update')
        ->setDescription('Collect timeline of most influencial users.')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln("Updating Twitter user timeline.");

        $logger = new LoggerOutputAdapter($output);
        $rep = $this->em->getRepository(TwitterUser::class);
        $rep->setLogger($logger);
        do {
            
            $rep->updateAllTimelines();  
            sleep(5);
            
        }
        while(true);

    }

}