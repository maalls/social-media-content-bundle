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

        $cache_dir = $this->api->getCacheLocation();

        $dir = new \DirectoryIterator($cache_dir);
        
        $skip = true;
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                echo $fileinfo->getFilename() . PHP_EOL;
                $filename = $cache_dir . "/" . $fileinfo->getFilename();

                $dataDatime = new \Datetime(date("Y-m-d H:i:s", filemtime($filename)));
                
                if(true || $fileinfo->getFilename() == "8b595cdbcff7022bdf11d3a303e104355bc87d09.json") {

                    $skip = false;

                }

                if($skip) continue;

                $json = json_decode(file_get_contents($filename));

                // if array can be list of tweet or list of profile.

                if(is_array($json)) {


                    if(!isset($json[0])) {

                        // empty array
                        unlink($filename);
                        continue;

                    }

                    $first = $json[0];
                    if(isset($first->screen_name)) {

                        // user lookup.

                        foreach($json as $user) {

                            $this->collectEntities($user, $dataDatime);
                            
                        }
                        unlink($filename);
                        continue;

                    }
                    elseif(isset($first->text)) {

                        // tweet timeline list. Only for existing user.
                        unlink($filename);
                        continue;

                    }



                }
                elseif(isset($json->screen_name)) {

                    // user
                    $this->collectEntities($json, $dataDatime);
                    unlink($filename);
                    continue;

                }
                elseif(isset($json->ids)) {

                    unlink($filename);
                    // list of ids, nothing to do
                    continue;

                }
                elseif(isset($json->error) || isset($json->errors)) {

                    // error response
                    unlink($filename);
                    continue;

                }


                var_dump($json);
                var_dump("To handle.");
                exit;



            }
        }

    }

    public function collectEntities($user, $dataDatime)
    {

        

        /*if(isset($user->status) && !isset($user->status->retweeted_status)) {

            $u = clone $user;
            $status = $u->status;
            $u->status = null;
            $status->user = $u;
            $t = $this->em->getRepository(Tweet::class)->generateFromJson($status, $dataDatime);
        
            echo $user->screen_name . " : updating status" . PHP_EOL;
            $this->em->flush();
            
        }*/

        $entities = $user->entities;

        if(isset($entities->description)) {

            if(isset($entities->description->urls)) {

                if($entities->description->urls) {

                    //var_dump($entities->description->urls);
                    //exit;

                }


            }

        }

        if(isset($entities->url)) {

            $count = count($entities->url->urls);

            $url = $entities->url->urls[0]->expanded_url;
            echo $user->screen_name . " (" . $count . ") : " . $url .  PHP_EOL;
            if($count > 1) {

                var_dump($user);
                exit;

            }
            else {

                $this->stmt->execute([$url, $user->id_str]);
                

            }

        }

        
    }

}