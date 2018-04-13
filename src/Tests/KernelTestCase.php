<?php 

// tests/Util/CalculatorTest.php
namespace Maalls\SocialMediaContentBundle\Tests;

class KernelTestCase extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function writeln($message)
    {

        fwrite(STDERR, $message);

    }

    public function getEntityManager()
    {

        return $this->em;

    }

    public function truncate($class) {

        return $this->em->getRepository($class)->createQueryBuilder("e")
            ->delete()->where("e.id is not null")
            
            ->getQuery()->getResult();

    }

    public function truncateAll()
    {

        $this->truncate(\Maalls\SocialMediaContentBundle\Entity\Tweet::class);
        $this->truncate(\Maalls\SocialMediaContentBundle\Entity\TwitterUserFollower::class);
        $this->truncate(\Maalls\SocialMediaContentBundle\Entity\TwitterUser::class);
        $this->truncate(\Maalls\SocialMediaContentBundle\Entity\Search::class);
        $this->truncate(\Maalls\SocialMediaContentBundle\Entity\InstagramCount::class);
        $this->truncate(\Maalls\SocialMediaContentBundle\Entity\TwitterStream::class);

    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}