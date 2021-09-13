<?php


namespace SQLI\EzToolboxBundle\Controller;


use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchAdapter;
use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchHitAdapter;
use eZ\Publish\Core\Pagination\Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Symfony\Component\HttpKernel\KernelInterface;


class ExtractDataController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /** @var SearchService */
    private $searchService;
    /** @var ContentTypeService */
    private $contentTypeService;

    public const CONTENT_TYPE_DISPLAY = 1;
    public const CONTENT_DISPLAY = 2;

    /**
     * SQLIContentListController constructor.
     * @param SearchService $searchService
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(
        SearchService $searchService,
        ContentTypeService $contentTypeService
    ) {
        $this->searchService = $searchService;
        $this->contentTypeService = $contentTypeService;
    }

    public function migrationType($type, $contentTypeGroupIdentifier){

        $params = [];
        $contentTypes = [];
        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups();
        foreach ($contentTypeGroups as $group) {
            $contentTypes[$group->identifier] = $this->contentTypeService->loadContentTypes($group);
        }
        $params['contentTypeGroups'] = $contentTypeGroups;
        $params['contentTypes'] = $contentTypes;

        return $this->render(
            '@SQLIEzToolbox/GenerateMigration/migrationType.html.twig',
            $params
        );
    }

    /**
     * @param KernelInterface $kernel
     * @return Response
     * @throws \Exception
     */
    public function migrationCommand($type, $contentTypeGroupIdentifier, KernelInterface $kernel): Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $contentTypes = [];
        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups();
        foreach ($contentTypeGroups as $group) {
            $contentTypes[$group->identifier] = $this->contentTypeService->loadContentTypes($group);
        }
        $matchValueOption = '';
        if ($contentTypeGroupIdentifier !== 'false' ) {
            foreach ($contentTypes[$contentTypeGroupIdentifier] as $contentType) {
                if ($matchValueOption != '') {
                    $matchValueOption .= ',';
                }
                $matchValueOption .= $contentType->identifier;
            }
        } else {
            foreach ($contentTypes as $contentType) {
                foreach ($contentType as $elt) {
                    if ($matchValueOption != '') {
                        $matchValueOption .= ',';
                    }
                    $matchValueOption .= $elt->identifier;
                }
            }
        }
        if ($type == ExtractDataController::CONTENT_TYPE_DISPLAY) {
            $input = new ArrayInput(
                [
                    'command' => 'sqli:migration:generate',
                    '--type' => 'content_type',
                    '--match-type' => 'contenttype_identifier',
                    '--match-value' => $matchValueOption,
                    'bundle' => 'SQLIEzToolboxBundle'
                ]
            );
        }
        elseif ($type == ExtractDataController::CONTENT_DISPLAY) {
            $input = new ArrayInput(
                [
                    'command' => 'sqli:migration:generate',
                    '--type' => 'content',
                    '--match-type' => 'contenttype_identifier',
                    '--match-value' => $matchValueOption,
                    'bundle' => 'SQLIEzToolboxBundle'
                ]
            );
        }

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        // return new Response(""), if you used NullOutput()
        return new Response($content);
    }

}