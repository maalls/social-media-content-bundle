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
use Maalls\SocialMediaContentBundle\Entity\Tweet;

class TmpCommand extends Command
{

    public function __construct(\Doctrine\Common\Persistence\ObjectManager $em, \Maalls\SocialMediaContentBundle\Lib\Twitter\Api $api)
    {

        parent::__construct();
        $this->em = $em;
        $this->api = $api;
        $this->stmt = $this->em->getConnection()->prepare("update twitter_user set url = ? where id = ?");

    }

    protected function configure()
    {
        $this
        ->setName('smc:tmp')
        ->setDescription('Tmp command.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        
        do {

            $tweet = $this->em->getRepository(Tweet::class)->find(21382611);

            echo $tweet->getText() . PHP_EOL;

            $this->em->clear(Tweet::class);

            sleep(3);

        }
        while(true);

    }


}