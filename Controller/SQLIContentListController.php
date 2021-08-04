<?php


namespace SQLI\EzToolboxBundle\Controller;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchAdapter;
use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchHitAdapter;
use eZ\Publish\Core\Pagination\Pagerfanta\Pagerfanta;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SQLIContentListController extends Controller
{
    private $searchService;
    private $contentTypeService;
    private $fieldTypeService;
    private $repository;

    /**
     * SQLIContentListController constructor.
     * @param $searchService
     * @param $contentTypeService
     */
    public function __construct(
        SearchService $searchService,
        ContentTypeService $contentTypeService,
        Repository $repository,
        FieldTypeService $fieldTypeService
    ) {
        $this->searchService = $searchService;
        $this->contentTypeService = $contentTypeService;
        $this->repository = $repository;
        $this->fieldTypeService = $fieldTypeService;
    }

    /**
     * @param bool $contentTypeIdentifier
     * @param int $page
     * @return Response
     * @throws InvalidArgumentException
     */
    public function listAction($contentTypeIdentifier = false, int $page = 1)
    {
        $query = new LocationQuery();

        $criterions = [
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
        ];

        if ($contentTypeIdentifier) {
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
                'articles' => $paginator,
                'contentTypes' => $contentTypes,
            ]
        );
    }

    /**
     *
     */
    public function getPaginator($contentTypeIdentifier, $maxPerPage = 8)
    {
        $query = new LocationQuery();

        $criterions = [
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
        ];

        if ($contentTypeIdentifier) {
            $criterions[] = new Criterion\ContentTypeIdentifier($contentTypeIdentifier);
        }

        $query->query = new Criterion\LogicalAnd($criterions);

        $paginator = new Pagerfanta(
            new LocationSearchAdapter($query, $this->searchService)
        );

        if ($maxPerPage !== false) {
            $paginator->setMaxPerPage(8);
        }
        return $paginator;
    }

    /**
     *
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     */
    public function exportCSV(
        $contentTypeIdentifier,
        ContentTypeService $contentTypeService,
        FieldTypeService $fieldTypeService
    ) {
        $response = new StreamedResponse();

        $query = new LocationQuery();
        $query->filter = new Criterion\ContentTypeIdentifier($contentTypeIdentifier);
        $result = $this->searchService->findContent($query, ['useAlwaysAvailable' => true]);

        $response->setCallback(
            function () use ($contentTypeService, $contentTypeIdentifier, $fieldTypeService, $result) {
                $fp = fopen('php://output', 'w');

                // Build Headers
                $headers = [];
                $contentType = $contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
                foreach ($contentType->fieldDefinitions as $fieldDefinition) {
                    array_push($headers, $fieldDefinition->identifier);
                }

                // Builder Columns
                $contents = array();
                foreach ($result->searchHits as $searchHit) {
                    array_push(
                        $contents,
                        $searchHit->valueObject
                    );
                }

                fputcsv($fp, $headers);

                $columns = [];
                foreach ($contents as $content) {
                    $column = [];
                    foreach ($contentType->fieldDefinitions as $fieldDefinition) {
                        $fieldType = $fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);
                        $field = $content->getField($fieldDefinition->identifier);
                        $valueHash = $fieldType->toHash($field->value);
                        if (is_array($valueHash)) {
                            $result = "";
                            array_walk_recursive(
                                $valueHash,
                                function ($elt) use (&$result) {
                                    $result .= strval($elt) . "\n";
                                }
                            );
                            array_push($column, $result);
                        } else {
                            array_push($column, $valueHash);
                        }
                    }
                    array_push($columns, $column);
                    fputcsv($fp, array_values($column), ';', '"');
                }

                fclose($fp);
            }
        );

        $filename = 'Content_List' . $contentTypeIdentifier;
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"export-{$filename}.csv\"");
        $response->setStatusCode(200);
        return $response;
    }
}