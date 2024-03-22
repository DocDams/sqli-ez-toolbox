<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Services\Listener;

use Exception;
use Ibexa\Contracts\Core\Repository\Events\Content\CopyContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\HideContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\RevealContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\CopySubtreeEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\CreateLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\DeleteLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\HideLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\MoveSubtreeEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\UnhideLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\SetContentStateEvent;
use Ibexa\Contracts\Core\Repository\Events\Section\AssignSectionToSubtreeEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\TrashEvent;
use Ibexa\Contracts\Core\Repository\Events\User\CreateUserEvent;
use Ibexa\Contracts\Core\Repository\Events\User\DeleteUserEvent;
use Ibexa\Contracts\Core\Repository\Events\User\UpdateUserEvent;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\MVC\Symfony\Security\UserInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
//use Netgen\TagsBundle\API\Repository\Events\Tags\CreateTagEvent;
//use Netgen\TagsBundle\API\Repository\Events\Tags\DeleteTagEvent;
//use Netgen\TagsBundle\API\Repository\Events\Tags\UpdateTagEvent;
//use Netgen\TagsBundle\API\Repository\TagsService;
use SQLI\EzToolboxBundle\Services\Formatter\SqliSimpleLogFormatter;
use SQLI\EzToolboxBundle\Services\SiteAccessUtilsTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\EventDispatcher\Event;

class BackOfficeActionsLoggerListener implements EventSubscriberInterface
{
    use SiteAccessUtilsTrait;

    private Logger $logger;

    private Request $request;

    private bool $adminLoggerEnabled;

    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private Repository $repository,
        $logDir,
        RequestStack $requestStack,
        $adminLoggerEnabled,
        SiteAccess $siteAccess
    ) {
        $this->request = $requestStack->getCurrentRequest();
        $this->adminLoggerEnabled = (bool)$adminLoggerEnabled;

        // Handler and formatter
        $logHandler = new StreamHandler(
            sprintf(
                "%s/log_%s-%s.log",
                $logDir,
                $siteAccess->name,
                date("Y-m-d")
            )
        );
        $logHandler->setFormatter(new SqliSimpleLogFormatter());

        $this->logger = new Logger('Log_' . $siteAccess->name);
        $this->logger->pushHandler($logHandler);
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     * The array keys are event names and the value can be:
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     * For instance:
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PublishVersionEvent::class => 'logIfPublishVersionEvent',
            CopyContentEvent::class => 'logIfCopyContentEvent',
            DeleteContentEvent::class => 'logIfDeleteContentEvent',
//            CreateTagEvent::class => 'logIfCreateTagEvent',
//            UpdateTagEvent::class => 'logIfUpdateTagEvent',
//            DeleteTagEvent::class => 'logIfDeleteTagEvent',
            MoveSubtreeEvent::class => 'logIfMoveSubtreeEvent',
            CopySubtreeEvent::class => 'logIfCopySubtreeEvent',
            CreateLocationEvent::class => 'logIfCreateLocationEvent',
            DeleteLocationEvent::class => 'logIfDeleteLocationEvent',
            HideLocationEvent::class => 'logIfVisibilityLocationEvent',
            UnhideLocationEvent::class => 'logIfVisibilityLocationEvent',
            HideContentEvent::class => 'logIfVisibilityContentEvent',
            RevealContentEvent::class => 'logIfVisibilityContentEvent',
            UpdateUserEvent::class => 'logIfUserEvent',
            CreateUserEvent::class => 'logIfUserEvent',
            DeleteUserEvent::class => 'logIfUserEvent',
            TrashEvent::class => 'logIfTrashEvent',
            AssignSectionToSubtreeEvent::class => 'logIfAssignSectionEvent',
            SetContentStateEvent::class => 'logIfSetContentStateEvent',
        ];
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function logIfPublishVersionEvent(PublishVersionEvent $event): void
    {
        // Log only for admin siteaccesses
        if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
            return;
        }

        $contentId = $event->getContent()->id;
        $versionId = $event->getVersionInfo()->id;
        $this->logger->notice("Content publish :");
        $this->logUserInformations();
        try {
            $content = $this->repository->getContentService()->loadContent($contentId, [], $versionId);
            $this->logger->notice("  - content name : " . $content->getName());
        } catch (Exception) {
            $this->logger->error("  - content : not found");
        }
        $this->logger->notice("  - content id : " . $contentId);
        $this->logger->notice("  - content version : " . $versionId);
    }

    /**
     * Log connected user informations
     */
    private function logUserInformations(): void
    {
        /** @var UserInterface $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $this->logger->notice("  - IP : " . implode(',', $this->request->getClientIps()));
        $this->logger->notice(
            sprintf(
                "  - user name : %s [contentId=%s]",
                $user->getUsername(),
                $user->getAPIUser()->getUserId()
            )
        );
    }

    /**
     * @throws NotFoundException
     */
    public function logIfCopyContentEvent(CopyContentEvent $event): void
    {
        // Log only for admin siteaccesses
        if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
            return;
        }

        $srcContentId = $event->getContent()->id;
        $srcVersionId = $event->getVersionInfo()->versionNo;
        $dstParentLocationId = $event->getDestinationLocationCreateStruct()->parentLocationId;
        $this->logger->notice("Content copy :");
        $this->logUserInformations();
        try {
            $this->logger->notice("  - content name : " . $event->getContent()->getName());
        } catch (Exception) {
            $this->logger->error("  - content : not found");
        }
        $this->logger->notice("  - original content id : " . $srcContentId);
        $this->logger->notice("  - original content version : " . $srcVersionId);

        try {
            $dstParentLocation = $this->repository->getLocationService()->loadLocation($dstParentLocationId);
            $this->logger->notice("  - destination parent location id : $dstParentLocationId");
            $this->logger->notice(
                "  - destination parent content name : " . $dstParentLocation->getContent()->getName()
            );
        } catch (Exception) {
            $this->logger->error("  - destination parent location : not found");
        }
    }

    /**
     * @throws NotFoundException
     */
    public function logIfDeleteContentEvent(DeleteContentEvent $event): void
    {
        // Log only for admin siteaccesses
        if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
            return;
        }

        $this->logger->notice("Content delete :");
        $this->logUserInformations();
        $this->logger->notice("  - content id : " . $event->getContentInfo()->id);
        $this->logger->notice("  - content name : " . $event->getContentInfo()->name);
        $this->logger->notice("  - location ids : " . implode(',', $event->getLocations()));
    }

    /*public function logIfCreateTagEvent(CreateTagEvent $event)
        {
            // Log only for admin siteaccesses
            if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
                return;
            }

            $parentTagName = "no parent";
            if ($event->getTag()->hasParent()) {
                $parentTagName = $this->tagsService->loadTag($event->getTag()->parentTagId)->getKeyword();
            }
            $this->logger->notice("Tag creation :");
            $this->logUserInformations();
            $this->logger->notice("  - tag id : " . $event->getTag()->id);
            $this->logger->notice("  - tag name : " . $event->getTag()->getKeyword());
            $this->logger->notice("  - tag parent id : " . $event->getTag()->parentTagId);
            $this->logger->notice("  - tag parent name : " . $parentTagName);
        }*/
    /*public function logIfUpdateTagEvent(UpdateTagEvent $event)
        {
            // Log only for admin siteaccesses
            if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
                return;
            }

            $this->logger->notice("Tag update :");
            $this->logUserInformations();
            $this->logger->notice("  - tag id : " . $event->getTag()->id);
            $this->logger->notice("  - new tag name : " . $event->getTag()->getKeyword());
        }*/
    /*public function logIfDeleteTagEvent(DeleteTagEvent $event)
        {
            // Log only for admin siteaccesses
            if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
                return;
            }

            $this->logger->notice("Tag delete :");
            $this->logUserInformations();
            $this->logger->notice("  - tag id : " . $event->getTag()->id);
        }*/
    /**
     * @throws NotFoundException
     */
    public function logIfMoveSubtreeEvent(MoveSubtreeEvent $event): void
    {
        // Log only for admin siteaccesses
        if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
            return;
        }

        $this->logger->notice("Location move :");
        $this->logUserInformations();
        $this->logger->notice("  - location id : " . $event->getLocation()->id);

        // New parent
        $this->logger->notice("  - new parent location id : " . $event->getNewParentLocation()->id);
        $this->logger->notice(
            "  - new parent content name : " . $event->getNewParentLocation()->getContent()->getName()
        );
    }

    /**
     * @throws NotFoundException
     */
    public function logIfCopySubtreeEvent(CopySubtreeEvent $event): void
    {
        // Log only for admin siteaccesses
        if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
            return;
        }

        $this->logger->notice("Location copy :");
        $this->logUserInformations();

        // Original parent
        $this->logger->notice("  - original location id : " . $event->getSubtree()->id);
        $this->logger->notice("  - original content name : " . $event->getSubtree()->getContent()->getName());

        // New Parent
        $this->logger->notice("  - copy's parent location id : " . $event->getTargetParentLocation()->id);
        $this->logger->notice(
            "  - copy's parent content name : " . $event->getTargetParentLocation()->getContent()->getName()
        );

        // New Location
        $this->logger->notice("  - copy's location id : " . $event->getLocation()->id);
    }

    /**
     * @throws NotFoundException
     */
    public function logIfCreateLocationEvent(CreateLocationEvent $event): void
    {
        // Log only for admin siteaccesses
        if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
            return;
        }

        $this->logger->notice("Location create :");
        $this->logUserInformations();

        $this->logger->notice("  - location id : " . $event->getLocation()->id);
        $this->logger->notice("  - content id : " . $event->getContentInfo()->id);
        $this->logger->notice("  - content name : " . $event->getContentInfo()->name);

        // New Parent
        $this->logger->notice("  - new parent location id : " . $event->getLocation()->id);
        try {
            $newParentLocation = $this->repository->getLocationService()->loadLocation(
                $event->getLocation()->parentLocationId
            );
            $this->logger->notice(
                "  - new parent content name : " . $newParentLocation->getContent()->getName()
            );
        } catch (Exception) {
            $this->logger->error("  - new parent content : not found");
        }
    }

    /**
     * @throws NotFoundException
     */
    public function logIfDeleteLocationEvent(DeleteLocationEvent $event): void
    {
        // Log only for admin siteaccesses
        if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
            return;
        }

        $this->logger->notice("Location delete :");
        $this->logUserInformations();
        $this->logger->notice("  - location id : " . $event->getLocation()->id);
        $this->logger->notice("  - parent location id : " . $event->getLocation()->parentLocationId);
        $this->logger->notice("  - content id : " . $event->getLocation()->contentId);
        $this->logger->notice("  - content name : " . $event->getLocation()->getContentInfo()->name);
    }

    /**
     * @throws NotFoundException
     */
    public function logIfVisibilityLocationEvent(Event $event): void
    {
        // Log only for admin siteaccesses
        if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
            return;
        }

        $actionName = null;
        if ($event instanceof HideLocationEvent) {
            $actionName = "hide";
            $location = $event->getHiddenLocation();
        }
        if ($event instanceof UnhideLocationEvent) {
            $actionName = "unhide";
            $location = $event->getRevealedLocation();
        }
        if (
            !is_null($actionName) &&
            isset($location) &&
            $location instanceof Location
        ) {
            $this->logger->notice("Location $actionName :");
            $this->logUserInformations();
            $this->logger->notice("  - location id : " . $location->id);
            $this->logger->notice("  - parent location id : " . $location->parentLocationId);
            $this->logger->notice("  - content id : " . $location->contentId);
            $this->logger->notice("  - content name : " . $location->getContentInfo()->name);
        }
    }

    /**
     * @throws NotFoundException
     */
    public function logIfVisibilityContentEvent(Event $event): void
    {
        // Log only for admin siteaccesses
        if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
            return;
        }

        $actionName = null;
        if ($event instanceof HideContentEvent) {
            $actionName = "hide";
            $contentInfo = $event->getContentInfo();
        }
        if ($event instanceof RevealContentEvent) {
            $actionName = "unhide";
            $contentInfo = $event->getContentInfo();
        }
        if (
            !is_null($actionName) &&
            isset($contentInfo) &&
            $contentInfo instanceof ContentInfo
        ) {
            $this->logger->notice("Content $actionName :");
            $this->logUserInformations();
            $this->logger->notice("  - content id : " . $contentInfo->id);
            $this->logger->notice("  - content name : " . $contentInfo->name);
        }
    }

    /**
     * @throws NotFoundException
     */
    public function logIfUserEvent(Event $event): void
    {
        // Log only for admin siteaccesses
        if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
            return;
        }

        $actionName = null;
        $actionName = $event instanceof UpdateUserEvent ? "update" : $actionName;
        $actionName = $event instanceof CreateUserEvent ? "creation" : $actionName;
        $actionName = $event instanceof DeleteUserEvent ? "delete" : $actionName;

        if (!is_null($actionName)) {
            /** @var UpdateUserEvent|CreateUserEvent|DeleteUserEvent $event */
            $user = $event->getUser();
            $this->logger->notice("User $actionName :");
            $this->logUserInformations();
            $this->logger->notice("  - user id : " . $user->id);
            $this->logger->notice("  - user name : " . $user->getName());
        }
    }

    /**
     * @throws NotFoundException
     */
    public function logIfTrashEvent(TrashEvent $event): void
    {
        // Log only for admin siteaccesses
        if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
            return;
        }

        $this->logger->notice("Trash :");
        $this->logUserInformations();
        $this->logger->notice("  - content id : " . $event->getTrashItem()->getContentInfo()->id);
        $this->logger->notice("  - content name : " . $event->getTrashItem()->getContentInfo()->name);
        $this->logger->notice("  - location id : " . $event->getLocation()->id);
        $this->logger->notice("  - parent location id : " . $event->getLocation()->parentLocationId);
        try {
            $location = $this->repository->getLocationService()->loadLocation(
                $event->getLocation()->parentLocationId
            );
            $this->logger->notice("  - parent content name : " . $location->getContent()->getName());
        } catch (Exception) {
            $this->logger->error("  - parent content : not found");
        }
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function logIfAssignSectionEvent(AssignSectionToSubtreeEvent $event): void
    {
        // Log only for admin siteaccesses
        if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
            return;
        }

        $this->logger->notice("Assign section :");
        $this->logUserInformations();
        $this->logger->notice("  - location id : " . $event->getLocation()->id);
        $this->logger->notice("  - location name : " . $event->getLocation()->getContentInfo()->name);
        $this->logger->notice("  - section id : " . $event->getSection()->identifier);
        $this->logger->notice("  - section name : " . $event->getSection()->name);
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function logIfSetContentStateEvent(SetContentStateEvent $event): void
    {
        // Log only for admin siteaccesses
        if (!$this->adminLoggerEnabled || !$this->isAdminSiteAccess()) {
            return;
        }

        if ($event instanceof SetContentStateEvent) {
            $this->logger->notice("Change object state :");
            $this->logUserInformations();
            $this->logger->notice("  - content id : " . $event->getContentInfo()->id);
            // Content
            $this->logger->notice("  - content name : " . $event->getContentInfo()->name);
            // Object state group
            $this->logger->notice("  - object state group name : " . $event->getObjectStateGroup()->getName());
            // Object state
            $this->logger->notice("  - object state name : " . $event->getObjectState()->getName());
        }
    }
}
