<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Services\Parameter;

use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Persistence\Cache\ObjectStateHandler;
use SQLI\EzToolboxBundle\Exceptions\ParameterHandlerContentExpectedException;
use SQLI\EzToolboxBundle\Exceptions\ParameterHandlerDataUnexpectedException;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ParameterHandlerAbstractObjectState implements ParameterHandlerInterface
{
    public const PARAMETER_NAME = "";

    protected ObjectStateService $objectStateService;

    public function __construct(
        protected Repository $repository,
        protected ObjectStateHandler $stateHandler,
        protected EntityManagerInterface $entityManager
    ) {
        $this->objectStateService = $this->repository->getObjectStateService();
    }

    /**
     * @return array
     * @throws NotFoundException
     */
    public function listParameters(): array
    {
        $groupState = $this->stateHandler->loadGroupByIdentifier($this::PARAMETER_NAME);
        $objectStates = $this->stateHandler->loadObjectStates($groupState->id);

        $parameterValues = [];
        foreach ($objectStates as $objectState) {
            $parameterValues[] = $objectState->identifier;
        }

        return $parameterValues;
    }

    /**
     * @param $paramName
     * @param $paramValue
     * @param OutputInterface|null $output
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function showParameter($paramName, $paramValue, OutputInterface $output = null): mixed
    {
        $groupState = $this->stateHandler->loadGroupByIdentifier($paramName);
        $objectState = $this->stateHandler->loadByIdentifier($paramValue, $groupState->id);

        $offset = 0;
        $items = null;
        $fetchParams = [new Criterion\ObjectStateId($objectState->id)];
        $total = $this->count($fetchParams);

        $output->writeln("$total contents with parameter $paramName=$paramValue :\n");
        do {
            // Fetch small group of contents
            $items = $this->fetch($fetchParams, $offset);

            // Publish each content
            foreach ($items as $content) {
                /** @var $content ContentInfo */
                $output->writeln("  - [{$content->id}] " . $content->name);
            }

            $offset += 50;
        } while ($offset < $total);
        return null;
    }

    /**
     * @return int
     * @throws InvalidArgumentException
     */
    protected function count(array $params = []): int
    {
        // Prepare count
        $query = new Query();

        $query->query = new Criterion\LogicalAnd($params);
        $query->performCount = true;
        $query->limit = 0;
        $results = $this->repository->getSearchService()->findContentInfo($query);

        return (int)$results->totalCount;
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    protected function fetch(array $params = [], int $offset = 0, int $limit = 25): array
    {
        // Prepare fetch with offset and limit
        $query = new Query();

        $query->query = new Criterion\LogicalAnd($params);
        $query->limit = $limit;
        $query->offset = $offset;
        $results = $this->repository->getSearchService()->findContentInfo($query);
        $items = [];

        // Prepare an array with contents
        foreach ($results->searchHits as $item) {
            $items[] = $item->valueObject;
        }

        return $items;
    }

    /**
     * @param                      $paramName
     * @param                      $paramValue
     * @param                      $contentIds
     * @param OutputInterface|null $output
     * @return array|true
     * @throws ParameterHandlerContentExpectedException
     */
    public function setParameter($paramName, $paramValue, $contentIds, OutputInterface $output = null): bool|array
    {
        if (is_null($contentIds)) {
            throw new ParameterHandlerContentExpectedException("No content specified to applied parameter");
        }

        $errors = [];

        try {
            $groupState = $this->stateHandler->loadGroupByIdentifier($paramName);
            $objectState = $this->stateHandler->loadByIdentifier($paramValue, $groupState->id);

            $groupState = $this->objectStateService->loadObjectStateGroup($groupState->id);
            $objectState = $this->objectStateService->loadObjectState($objectState->id);

            $contentIds = explode(",", (string) $contentIds);

            foreach ($contentIds as $index => $contentId) {
                // Get content
                $content = $this->repository->getContentService()->loadContent(intval($contentId));

                // Change object state if a Content found else log an error
                // Change content's Object State
                $this->objectStateService->setContentState($content->contentInfo, $groupState, $objectState);

                if (!is_null($output)) {
                    $output->writeln(sprintf(
                        "[%d/%d] Set %s=%s for contentID %d : %s",
                        str_pad(
                            strval($index + 1),
                            strlen((string)count($contentIds)),
                            " ",
                            STR_PAD_LEFT
                        ),
                        count($contentIds),
                        $paramName,
                        $paramValue,
                        $contentId,
                        $content->getName()
                    ));
                }
            }
        } catch (\Exception $exception) {
            $errors[] = $exception->getMessage();
        }

        return count($errors) ? $errors : true;
    }

    /**
     * @param OutputInterface|null $output
     * @throws ParameterHandlerDataUnexpectedException
     */
    public function getData(OutputInterface $output = null): mixed
    {
        throw new ParameterHandlerDataUnexpectedException("Data not implemented for object state parameter");
    }

    /**
     * @param $data
     * @param OutputInterface|null $output
     * @throws ParameterHandlerDataUnexpectedException
     */
    public function setData($data, OutputInterface $output = null): mixed
    {
        throw new ParameterHandlerDataUnexpectedException("Data not implemented for object state parameter");
    }

    /**
     * @param OutputInterface|null $output
     * @throws ParameterHandlerDataUnexpectedException
     */
    public function showData(OutputInterface $output = null): mixed
    {
        throw new ParameterHandlerDataUnexpectedException("Data not implemented for object state parameter");
    }
}
