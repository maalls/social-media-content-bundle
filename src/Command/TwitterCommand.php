<?php

namespace Maalls\SocialMediaContentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Maalls\SocialMediaContentBundle\Lib\LoggerOutputAdapter;
use Maalls\SocialMediaContentBundle\Lib\Twitter\Search;
use Maalls\SocialMediaContentBundle\Entity\TwitterUser;
use Maalls\SocialMediaContentBundle\Entity\TwitterUserFollower;


class TwitterCommand extends Command
{

    public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
    {

        parent::__construct();
        $this->em = $em;

    }

    protected function configure()
    {
        $this
        ->setName('smc:twitter')
        ->setDescription('Twitter command.')
        ->addArgument('action', InputArgument::REQUIRED, 'What action to perform?')
        ->addArgument('parameters', InputArgument::REQUIRED, 'action parameters.')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Starting search");

        $logger = new LoggerOutputAdapter($output);
       
        

        switch($input->getArgument("action")) {

            case 'followers':

                $screenName = $input->getArgument("parameters");
                $logger->log("Retriving " . $screenName);
                $user = $this->em->getRepository(TwitterUser::class)->generateFromScreenName($screenName);
                if($user) {
                    $followerRep = $this->em->getRepository(TwitterUserFollower::class);
                    $followerRep->setLogger($logger);
                    $logger->log("Collecting ~" . $user->getFollowersCount() . " followers");
                    $followerRep->generateFollowersFromTwitterUser($user, true);
                }
                else {

                    $logger->log("User do not exist.");

                }



        }

    }

}