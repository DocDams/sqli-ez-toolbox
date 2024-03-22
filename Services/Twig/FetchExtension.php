<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Services\Twig;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\View\ViewManagerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use SQLI\EzToolboxBundle\Services\FetchHelper;
use SQLI\EzToolboxBundle\Services\Formatter\SqliSimpleLogFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FetchExtension extends AbstractExtension
{
    private readonly LoggerInterface|Logger $logger;

    public function __construct(
        private readonly FetchHelper $fetchHelper,
        private readonly ViewManagerInterface $viewManager,
        private readonly Repository $repository,
        $logDir
    ) {
        $handler = new StreamHandler("$logDir/sqli-eztoolbox_" . date("Y-m-d") . '.log');
        $handler->setFormatter(new SqliSimpleLogFormatter());
        $this->logger = new Logger('SQLILogException');
        $this->logger->pushHandler($handler);
    }

    public function getFunctions(): array
    {
        return
            [
                new TwigFunction('render_children', $this->renderChildren(...), ['is_safe' => ['all']]),
                new TwigFunction('fetch_children', $this->fetchHelper->fetchChildren(...)),
                new TwigFunction('fetch_ancestor', $this->fetchHelper->fetchAncestor(...)),
                new TwigFunction('fetch_ancestors', $this->fetchHelper->fetchAncestors(...)),
                new TwigFunction('fetch_content', [$this->repository->getContentService(), 'loadContent']),
                new TwigFunction('fetch_location', [$this->repository->getLocationService(), 'loadLocation']),
            ];
    }

    /**
     * Use ViewController:viewLocation to generate display of children
     * (eventually filtered with $filterContentClass) of a $location in specified $viewType
     * Some $parameters can be passed to template
     *
     * @param $parentLocation
     * @param $filterContentClass
     * @return string
     * @throws InvalidArgumentException
     */
    public function renderChildren(
        $parentLocation,
        string $viewType = ViewManagerInterface::VIEW_TYPE_LINE,
        $filterContentClass = null,
        array $parameters = array()
    ): string {
        // Fetch children of $location
        $children = $this->fetchHelper->fetchChildren($parentLocation, $filterContentClass);

        $render = "";
        $lastKey = array_key_last($children);
        $firstKey = array_key_first($children);

        foreach ($children as $index => $child) {
            $isfirst = $index === $firstKey;
            $islast = $index === $lastKey;
            // Define specific parameters
            $specificParameters =
                [
                    'isFirst' => $isfirst,
                    'isLast' => $islast,
                    'index' => $index,
                ];

            try {
                $parameters['location'] = $child;
                $content = $child->getContent();
                $parameters['content'] = $content;
                $contentRender = $this->viewManager->renderContent(
                    $content,
                    $viewType,
                    array_merge($parameters, $specificParameters)
                );
                $render .= $contentRender;
            } catch (\Exception $exception) {
                $this->logger->critical("Exception thrown in " . __METHOD__);
                $this->logger->critical($exception->getMessage());
                $this->logger->critical($exception->getTraceAsString());
                continue;
            }
        }

        return $render;
    }

    public function getName(): string
    {
        return 'sqli_twig_extension_fetch';
    }
}
