<?php

namespace SQLI\EzToolboxBundle\Command;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LocationHideCommand extends Command
{
    /** @var Repository */
    protected $repository;
    /** @var LocationService */
    protected $locationService;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->locationService = $repository->getLocationService();
        parent::__construct('sqli:object:hide');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws NotFoundException
     */
    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $output->setDecorated(true);

        // Load and set Administrator User
        $administratorUser = $this->repository->getUserService()->loadUserByLogin('admin');
        $this->repository->getPermissionResolver()->setCurrentUserReference($administratorUser);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Hide location')
            ->addArgument('location', InputArgument::REQUIRED, "LocationID to hide");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if ($locationID = $input->getArgument('location')) {
            $output->write("Hide locationID $locationID : ");

            $location = $this->locationService->loadLocation($locationID);
            $contentName = $location->getContent()->getName();
            $this->locationService->hideLocation($location);
            $output->writeln("<info>" . $contentName . "</info>");
        }
    }
}
