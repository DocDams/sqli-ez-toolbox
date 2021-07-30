<?php


namespace SQLI\EzToolboxBundle\Controller;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchAdapter;
use eZ\Publish\Core\Pagination\Pagerfanta\Pagerfanta;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class SQLIContentListController extends Controller
{
    private $searchService;
    private $contentTypeService;
    private $repository;

    /**
     * SQLIContentListController constructor.
     * @param $searchService
     * @param $contentTypeService
     */
    public function __construct(SearchService $searchService, ContentTypeService $contentTypeService, Repository $repository)
    {
        $this->searchService = $searchService;
        $this->contentTypeService = $contentTypeService;
        $this->repository = $repository;
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

        return $this->render('@SQLIEzToolbox/ContentList/all_content_list.html.twig', [
            'totalCount' => $paginator->getNbResults(),
            'articles' => $paginator,
            'contentTypes' => $contentTypes,
        ]);

//        $contentTypes = [];
//        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups();
//        foreach ($contentTypeGroups as $group) {
//            $contentTypes[$group->identifier] = $this->contentTypeService->loadContentTypes($group);
//        }
//        dump($contentTypes);
//
//        $query = new LocationQuery();
//        $param = array();
//        foreach ($contentTypes["Content"] as $contentType) {
//            array_push($param, $contentType->identifier);
//        }
//
//        $query->query = new Criterion\LogicalAnd([new Criterion\ContentTypeIdentifier($param)]);
//        $query->performCount = true;
////        $query->limit = 0; //count only does not return data
////        $query->limit = ;
//
//        $this->searchService = $this->repository->getSearchService();
//        $results = $this->searchService->findContent($query);
//
//        $paginator = new Pagerfanta(
//            new LocationSearchAdapter($query, $this->searchService)
//        );
//        $paginator->setMaxPerPage(8);
//        $paginator->setCurrentPAge($page);
//
//        dump($query);
//        dump($results);
//        foreach ($paginator as $item) {
//            dump($item);
//        }
//        dump($paginator);
//
//        return $this->render('@SQLIEzToolbox/ContentList/all_content_list.html.twig', [
//            'contentTypeGroups' =>$contentTypeGroups,
//            'contentTypes' => $contentTypes,
//            'count' => (int)$results->totalCount,
//            'contents' => $paginator,
//        ]);

    }
}