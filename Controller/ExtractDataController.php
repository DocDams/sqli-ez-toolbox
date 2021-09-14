<?php


namespace SQLI\EzToolboxBundle\Controller;


use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchAdapter;
use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchHitAdapter;
use eZ\Publish\Core\Pagination\Pagerfanta\Pagerfanta;
use SQLI\EzToolboxBundle\Form\ContentMigration\GenerateMigrationFileType;
use SQLI\EzToolboxBundle\Form\EntityManager\EditElementType;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
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

    public function getContentTypes() {
        $contentTypes = [];
        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups();
        foreach ($contentTypeGroups as $group) {
            $contentTypes[$group->identifier] = $this->contentTypeService->loadContentTypes($group);
        }
        $result = [];
        $result['contentTypeGroups'] = $contentTypeGroups;
        $result['contentTypes'] = $contentTypes;
        return $result;
    }

    public function migrationType($type, $contentTypeGroupIdentifier, Request $request, KernelInterface $kernel){

        $params = [];
        $data = $this->getContentTypes();
        $contentTypes = $data['contentTypes']; $contentTypeGroups = $data['contentTypeGroups'];
        $params['contentTypeGroups'] = $contentTypeGroups;
        $params['contentTypes'] = $contentTypes;

        if ($contentTypeGroupIdentifier != 'false' ){
            $form = $this->createForm(GenerateMigrationFileType::class, null,
                                      [
                                          'contentTypes' => $contentTypes[$contentTypeGroupIdentifier],
                                          'contentTypeGroupIdentifier' => $contentTypeGroupIdentifier
                                          ]);
        } else {
            $form = $this->createForm(GenerateMigrationFileType::class, null, [
                'contentTypes' => $contentTypes,
                'contentTypeGroupIdentifier' => $contentTypeGroupIdentifier
            ]);
        }
        $form->handleRequest($request);
        $params['form'] = $form->createView();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($contentTypeGroupIdentifier == 'false') {
                $matchValueOptions = '';
                foreach ($contentTypes as $key => $contentType) {
                        if ($form->get($key)->getData()) {
                            foreach ($contentType as $elt) {
                                if ($matchValueOptions != '') {
                                    $matchValueOptions .= ',';
                                }
                                $matchValueOptions .= $elt->identifier;
                            }
                        }
                }
                try {
                    $this->migrationCommand($type, $matchValueOptions, $kernel);
                } catch (\Exception $e) {
                }
                return $this->redirectToRoute(
                    'sqli_eztoolbox_generate_migration',
                    [
                        'type' => ExtractDataController::CONTENT_TYPE_DISPLAY,
                        'contentTypeGroupIdentifier' => 'false'
                    ]
                );
            } else {
                $matchValueOptions = '';
                foreach ($contentTypes[$contentTypeGroupIdentifier] as $key => $contentType) {
                    if ($form->get($contentType->identifier)->getData()) {
                        if($matchValueOptions != ''){
                            $matchValueOptions .= ',';
                        }
                        $matchValueOptions .= $contentType->identifier;
                    }
                }
                try {
                    $this->migrationCommand($type, $matchValueOptions, $kernel);
                } catch (\Exception $e) {
                }
                return $this->redirectToRoute(
                    'sqli_eztoolbox_generate_migration',
                    [
                        'type' => ExtractDataController::CONTENT_DISPLAY,
                        'contentTypeGroupIdentifier' => $contentTypeGroupIdentifier
                    ]
                );
            }
        }
        return $this->render(
            '@SQLIEzToolbox/GenerateMigration/generateMigrationFileList.html.twig',
            $params
        );
    }

    /**
     * @param KernelInterface $kernel
     * @return Response
     * @throws \Exception
     */
    public function migrationCommand($type, $matchValueOptions , KernelInterface $kernel): Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $data = $this->getContentTypes();

        $input = new ArrayInput([]);
        if ($type == ExtractDataController::CONTENT_TYPE_DISPLAY) {
            $input = new ArrayInput(
                [
                    'command' => 'sqli:migration:generate',
                    '--type' => 'content_type',
                    '--match-type' => 'contenttype_identifier',
                    '--match-value' => $matchValueOptions,
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
                    '--match-value' => $matchValueOptions,
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