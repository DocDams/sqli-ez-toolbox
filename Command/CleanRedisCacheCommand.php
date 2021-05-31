<?php

namespace SQLI\EzToolboxBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Cache\CacheItemPoolInterface;

class CleanRedisCacheCommand extends ContainerAwareCommand
{
   /** @var CacheItemPoolInterface */
    private $cachePool;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName( 'sqli:clear_redis_cache' )
            ->setDescription( 'Clear Redis persistence cache' );
    }
     /**
     * @param ContainerInterface $container
     */
    public function __construct(CacheItemPoolInterface  $cachePool) {
        $this->cachePool = $cachePool;
        parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        // To clear all cache
         $this->cachePool->clear();

        // To clear a specific cache item (check source code in eZ\Publish\Core\Persistence\Cache\*Handlers for further info)
        //$this->cachePool->clear('content', 'info', $contentId);

        // Stash cache is hierarchical, so you can clear all content/info cache like so:
        //$this->cachePool->clear('content', 'info');
    }
}
