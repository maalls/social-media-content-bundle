<?php

namespace Maalls\SocialMediaContentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Maalls\SocialMediaContentBundle\Lib\LoggerOutputAdapter;
use Maalls\SocialMediaContentBundle\Entity\InstagramCount;
class InstagramCountCommand extends Command
{

    public function __construct(
        \Doctrine\Common\Persistence\ObjectManager $em, 
        \Maalls\SocialMediaContentBundle\Lib\Instagram\Factory $factory,
        \Maalls\SocialMediaContentBundle\Lib\Firebase\Factory $firebaseFactory,
        \Maalls\SocialMediaContentBundle\Lib\Counter $counter

    )
    {

        parent::__construct();
        $this->em = $em;
        $this->api = $factory->createApi();
        $this->firebase = $firebaseFactory->create();
        $this->counter = $counter;

    }

    protected function configure()
    {
        $this
        ->setName('smc:instagram:count')
        ->setDescription('Count instagram tags.')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        do {

            try {
                $instagramCounts = $this->em->getRepository(InstagramCount::class)
                    ->findAll();


                foreach($instagramCounts as $count) {

                    $tag = $count->getTag();

                    $output->writeln("Searching for " . $tag);
                    $rsp = $this->api->request("tags/search", ["q" => $tag]);
                
                    $rsp = json_decode($rsp);

                    foreach($rsp->data as $t) {

                        if($t->name == $tag) {

                            $count->setCount($t->media_count);
                            $count->setUpdatedAt(new \Datetime());
                            $this->em->persist($count);
                            $this->em->flush();
                            $firebaseCount = $count->getCount() - $count->getOffsetCount();
                            $rsp = $this->firebase->set("/bazooka/instagram", ["count" => $firebaseCount, "tag" => $count->getTag()]);
                            $rsp = $this->firebase->set("/bazooka/total/", ["count" => $this->counter->getCount()]);
                            
                            $output->writeln($count->getTag() . ": " . $count->getCount() . " contents found, " . $firebaseCount . " reported.");
                            break;

                        }

                    }

                    sleep(10);
                    $this->em->clear(InstagramCount::class);

                }
            }
            catch(\Exception $e) {

                $output->writeln("Error: " . $e->getMessage());
                sleep(30);

            }

        }
        while(true);

    }

}