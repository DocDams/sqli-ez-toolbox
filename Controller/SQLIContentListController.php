<?php


namespace SQLI\EzToolboxBundle\Controller;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchAdapter;
use eZ\Publish\Core\Pagination\Pagerfanta\Pagerfanta;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\Response;

class SQLIContentListController extends Controller
{
    /** @var SearchService */
    private $searchService;
    /** @var ContentTypeService */
    private $contentTypeService;

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

    /**
     * @param bool $contentTypeIdentifier
     * @param int $page
     * @return Response
     */
    public function listAction($contentTypeIdentifier = false, int $page = 1)
    {
        $query = new LocationQuery();

        $criterions = [
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
        ];

        if ($contentTypeIdentifier !== "false") {
            $criterions[] = new Criterion\ContentTypeIdentifier($contentTypeIdentifier);
        }

        $query->query = new Criterion\LogicalAnd($criterions);

        $paginator = new Pagerfanta(
            new LocationSearchAdapter($query, $this->searchService)
        );
        $paginator->setMaxPerPage(8);
        $paginator->setCurrentPage($page);

        $contentTypes = [];
        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups();
        foreach ($contentTypeGroups as $group) {
            $contentTypes[$group->identifier] = $this->contentTypeService->loadContentTypes($group);
        }

        return $this->render(
            '@SQLIEzToolbox/ContentList/all_content_list.html.twig',
            [
                'totalCount' => $paginator->getNbResults(),
                'paginator' => $paginator,
                'contentTypes' => $contentTypes,
                'selectedContentType' => $contentTypeIdentifier,
            ]
        );
    }

    /**
     *
     * @param $contentTypeIdentifier
     * @return BinaryFileResponse
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function exportCSV($contentTypeIdentifier): BinaryFileResponse
    {
        $filename = uniqid('/tmp/sqlitoolbox_') . ".csv";
        $query = new LocationQuery();
        $query->filter = new Criterion\ContentTypeIdentifier($contentTypeIdentifier);
        $result = $this->searchService->findContent($query, ['useAlwaysAvailable' => true]);

        $fp = fopen($filename, 'w');

        // Build Headers
        $headers = [];
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        foreach ($contentType->fieldDefinitions as $fieldDefinition) {
            array_push($headers, $fieldDefinition->identifier);
        }

        fputcsv($fp, $headers, ';');

        foreach ($result->searchHits as $searchHit) {
            /** @var Content $content */
            $content = $searchHit->valueObject;
            $row = [];
            foreach ($contentType->fieldDefinitions as $fieldDefinition) {
                $field = $content->getFieldValue($fieldDefinition->identifier);
                $fieldValue = $field->__toString();
                $row[] = $fieldValue;
            }
            fputcsv($fp, $row, ';');
        }

        fclose($fp);
        $stream = new Stream($filename);
        $response = new BinaryFileResponse($stream);

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set(
            'Content-Disposition',
            "attachment; filename=\"export-{$contentTypeIdentifier}-Contents.csv\""
        );
        return $response;
    }
}
