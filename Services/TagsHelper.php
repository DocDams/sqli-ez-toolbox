<?php

namespace SQLI\EzToolboxBundle\Services;

use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\API\Repository\Values\Tags\TagCreateStruct;
use Netgen\TagsBundle\API\Repository\Values\Tags\TagUpdateStruct;
use Netgen\TagsBundle\Core\Repository\TagsService;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Class TagsHelper
 * @package ExtranetBundle\Services
 */
class TagsHelper
{
    /** @var TagsService */
    private $tagsService;
    /** @var Repository */
    private $repository;

    /**
     * TagsHelper constructor.
     * @param TagsService $tagsService
     * @param Repository  $repository
     */
    public function __construct( ?TagsService $tagsService, Repository $repository )
    {
        $this->tagsService = $tagsService;
        $this->repository  = $repository;
    }

    /**
     * Return an array with all tags
     *
     * @return Tag[]
     * @throws RuntimeException
     * @throws UnauthorizedException
     */
    public function getAllTags()
    {
        if( !$this->tagsService instanceof TagsService )
        {
            throw new RuntimeException( "Bundle netgen/tagsbundle required to use this helper" );
        }
        return $this->tagsService->searchTags( "", "fre-FR" )->tags;
    }

    /**
     * Return all contents pages with specified tag ID or tag keyword or Tag object
     *
     * @param $tag
     * @return array
     * @throws RuntimeException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getContentFromTag( $tag )
    {
        if( !$this->tagsService instanceof TagsService )
        {
            throw new RuntimeException( "Bundle netgen/tagsbundle required to use this helper" );
        }

        // Retrieve all contents with this tag
        $objects      = $this->getContentsFromTag( $tag );
        $contentPages = [];

        /** @var $object Content */
        foreach( $objects as $object )
        {
            // Priority is fixed on location
            $location       = $this->repository->getLocationService()->loadLocation( $object->mainLocationId );
            $contentPages[] = [
                "content"  => $object,
                "priority" => $location->priority
            ];
        }

        // Sort contentInfo objects in $contentPages with their priority
        usort( $contentPages, function( $node1, $node2 )
        {
            if( $node1['priority'] < $node2['priority'] )
            {
                return 1;
            }
            elseif( $node1['priority'] > $node2['priority'] )
            {
                return -1;
            }
            else
            {
                return 0;
            }
        } );

        // Keep only contentInfo
        array_walk( $contentPages, function( &$node1, $index )
        {
            $node1 = $node1['content'];
        } );

        return $contentPages;
    }

    /**
     * Return all contents with specified tag ID or tag keyword or Tag object
     *
     * @param $tag
     * @return ContentInfo[]
     * @throws RuntimeException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getContentsFromTag( $tag )
    {
        if( !$this->tagsService instanceof TagsService )
        {
            throw new RuntimeException( "Bundle netgen/tagsbundle required to use this helper" );
        }

        $tagObject = $this->getTag( $tag );

        return $this->tagsService->getRelatedContent( $tagObject );
    }

    /**
     * Retrieve a tag from ID, Keyword or object
     *
     * @param $tag
     * @return Tag
     * @throws RuntimeException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getTag( $tag )
    {
        if( !$this->tagsService instanceof TagsService )
        {
            throw new RuntimeException( "Bundle netgen/tagsbundle required to use this helper" );
        }

        if( $tag instanceof Tag )
        {
            return $tag;
        }
        else
        {
            if( is_numeric( $tag ) )
            {
                $tagFound = $this->tagsService->loadTag( $tag );
            }
            else
            {
                $tagSearch = $this->tagsService->searchTags( $tag, "fre-FR" )->tags;
                $tagFound  = reset( $tagSearch );
            }

            return $tagFound;
        }
    }

    /**
     * @param array      $tagCreateStructure Array with the structure of the new tag
     * @param string|int $parentID Parent tag's ID or remoteID
     * @return Tag
     * @throws RuntimeException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     */
    public function createOrUpdateTag( $tagCreateStructure, $parentID )
    {
        if( !$this->tagsService instanceof TagsService )
        {
            throw new RuntimeException( "Bundle netgen/tagsbundle required to use this helper" );
        }

        // Defaults values for create structure override by parameters
        $tagCreateStructure = array_merge(
            [
                'parentTagId'      => 0,
                'mainLanguageCode' => 'fre-FR',
                'alwaysAvailable'  => true,
                'remoteId'         => null,
                'keywords'         =>
                    [
                        'fre-FR' => '',
                        'eng-GB' => '',
                    ],
            ], $tagCreateStructure );

        // Check given ID if it's a tagID or remoteID
        if( is_numeric( $parentID ) )
        {
            // TagID, reuse it
            $tagCreateStructure['parentTagId'] = $parentID;
        }
        else
        {
            // RemoteID, load Tag and retrieve it's ID
            $parentTag                         = $this->tagsService->loadTagByRemoteId( $parentID );
            $tagCreateStructure['parentTagId'] = $parentTag->id;
        }
        try
        {
            // Check if Tag already exists
            if( $tagCreateStructure['remoteId'] )
            {
                $tagUpdateStructure = $tagCreateStructure;
                // Parent Tag ID cannot be updated
                unset( $tagUpdateStructure['parentTagId'] );

                // Try to load the tag if exists
                $tagToUpdate        = $this->tagsService->loadTagByRemoteId( $tagUpdateStructure['remoteId'] );
                $tagUpdateStructure = new TagUpdateStruct( $tagUpdateStructure );

                // Update tag
                return $this->tagsService->updateTag( $tagToUpdate, $tagUpdateStructure );
            }
        }
        catch( NotFoundException $exception )
        {
            // Do nothing, try to create
        }

        $tagCreateStructure = new TagCreateStruct( $tagCreateStructure );

        // Create and returns new Tag
        return $this->tagsService->createTag( $tagCreateStructure );
    }
}