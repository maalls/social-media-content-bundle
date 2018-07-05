<?php

namespace Maalls\SocialMediaContentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Maalls\SocialMediaContentBundle\Lib\LoggerOutputAdapter;
use Maalls\SocialMediaContentBundle\Lib\Twitter\StreamFactory;
use Maalls\SocialMediaContentBundle\Entity\TwitterTrend;

class TwitterTrendCommand extends Command
{

    public function __construct(
        \Maalls\SocialMediaContentBundle\Lib\Twitter\Api $api,
        \Doctrine\ORM\EntityManagerInterface $em,
        \Maalls\SocialMediaContentBundle\Lib\Woeid $woeid

    )
    {

        parent::__construct();
        $this->api = $api;
        $this->em = $em;
        $this->woeid = $woeid;

    }

    protected function configure()
    {
        $this
        ->setName('smc:twitter:trend')
        ->setDescription('Collect Twitter trends.')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /*$rsp = $this->api->get("trends/available", []);

        echo json_encode($rsp) . PHP_EOL;
        */
        $locations = $this->woeid->getLocations();
        
        do {

            foreach($locations as $location) {

                $woeid = $location["woeid"];
                $time = time();
                $i = date("i", $time);
                $i = $i - ($i %5);
                $datetime = new \Datetime(date("Y-m-d H:$i:00", $time));

                $trends = $this->em->getRepository(TwitterTrend::class)->findBy(
                    ["woeid" => $woeid, "datetime" => $datetime]
                );

                if($trends) {

                    $output->writeln("Trend for $woeid aleady collected.");

                }
                else {

                    $this->api->setLogger(new LoggerOutputAdapter($output));
                    $this->api->setCacheDuration(60);
                    $rsp = $this->api->get("trends/place", ["id" => $woeid], false);
                    
                    foreach($rsp[0]->trends as $k => $entry) {

                        $trend = new TwitterTrend();
                        $trend->setRank($k + 1);
                        $trend->setWoeid($woeid);
                        $trend->setDatetime($datetime);
                        $trend->setName($entry->name);
                        $trend->setPromotedContent($entry->promoted_content);
                        $trend->setTweetVolume($entry->tweet_volume ? $entry->tweet_volume : 0);
                        $this->em->persist($trend);

                    }

                    $this->em->flush();

                }

                sleep(5);

            }

        }
        while(true);

    }

}