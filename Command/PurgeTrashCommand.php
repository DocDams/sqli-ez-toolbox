<?php

namespace SQLI\EzToolboxBundle\Command;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\SearchService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeTrashCommand extends Command
{
    /** @var Repository */
    protected $repository;
    /** @var ContentService */
    protected $contentService;
    /** @var SearchService */
    protected $searchService;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->contentService = $repository->getContentService();
        $this->searchService = $repository->getSearchService();
        parent::__construct('sqli:purge:trash');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws NotFoundException
     */
    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $output->setDecorated(true);

        // Load and set Administrator User for permissions
        $administratorUser = $this->repository->getUserService()->loadUserByLogin('admin');
        $this->repository->getPermissionResolver()->setCurrentUserReference($administratorUser);
    }

    protected function configure(): void
    {
        $this->setDescription('Purge eZ trash');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws UnauthorizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->repository->getTrashService()->emptyTrash();
        $output->writeln("Trash emptied");
    }
}
