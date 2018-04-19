<?php

namespace Maalls\SocialMediaContentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Maalls\SocialMediaContentBundle\Lib\LoggerOutputAdapter;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Search;
use Maalls\SocialMediaContentBundle\Entity\TwitterUser;


class TwitterUserProfileUpdateCommand extends Command
{

    public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
    {

        parent::__construct();
        $this->em = $em;

    }

    protected function configure()
    {
        $this
        ->setName('smc:twitter:user:update')
        ->setDescription('Update Twitter user profile command.')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Updating Twitter user profile.");

        $logger = new LoggerOutputAdapter($output);
        $rep = $this->em->getRepository(TwitterUser::class);
        $rep->setLogger($logger);
        do {
        
            $rep->updateProfiles();  
            sleep(5);

        }
        while(true);

    }

}