<?php

namespace SQLI\EzToolboxBundle\Services\Twig;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\User\UserReference;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension;
use SQLI\EzToolboxBundle\Services\DataFormatterHelper;
use SQLI\EzToolboxBundle\Services\FieldHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigFilterExtension extends AbstractExtension
{
    /** @var Repository */
    private $repository;
    /** @var DataFormatterHelper */
    private $dataFormatterHelper;
    /** @var ConfigResolverInterface */
    private $configResolver;
    /** @var FieldHelper */
    private $fieldHelper;
    /** @var ContentExtension */
    private $contentExtension;

    public function __construct( Repository $repository, DataFormatterHelper $dataFormatterHelper,
                                 ConfigResolverInterface $configResolver, FieldHelper $fieldHelper,
                                 ContentExtension $contentExtension )
    {
        $this->repository          = $repository;
        $this->dataFormatterHelper = $dataFormatterHelper;
        $this->configResolver      = $configResolver;
        $this->fieldHelper         = $fieldHelper;
        $this->contentExtension    = $contentExtension;
    }

    public function getFunctions()
    {
        return
            [
                new TwigFunction( 'format_data', [ $this->dataFormatterHelper, 'format' ] ),
                new TwigFunction( 'empty_field', [ $this, 'isEmptyField' ] ),
                new TwigFunction( 'is_anonymous_user', [ $this, 'isAnonymousUser' ] ),
                new TwigFunction( 'content_name', [ $this, 'getContentName' ] ),
                new TwigFunction( 'ez_parameter', [ $this, 'getParameter' ] ),
            ];
    }

    public function getFilters()
    {
        return
            [
                new TwigFilter( 'format_data', [
                    $this->dataFormatterHelper,
                    'format'
                ] ),
            ];
    }

    /**
     * Search a variable/parameter in eZ namespace
     *
     * @param $parameterName
     * @param $namespace
     * @return |null
     */
    public function getParameter( $parameterName, $namespace )
    {
        try
        {
            return $this->configResolver->getParameter( $parameterName, $namespace );
        }
        catch( \Exception $e )
        {
            return null;
        }
    }

    /**
     * Checks if a given field is considered empty.
     * This method accepts field as Objects or by identifiers.
     *
     * @param Content      $content
     * @param Field|string $fieldDefIdentifier Field or Field Identifier to get the value from.
     * @param string       $forcedLanguage Locale we want the content name translation in (e.g. "fre-FR").
     *                                     Null by default (takes current locale).
     *
     * @return bool
     */
    public function isEmptyField( Content $content, $fieldDefIdentifier, $forcedLanguage = null )
    {
        return $this->fieldHelper->isEmptyField( $content, $fieldDefIdentifier, $forcedLanguage );
    }

    /**
     * Get content name even if current user cannot access to this content
     *
     * @param $content Content|int Content or it's ID
     * @return string
     * @throws InvalidArgumentType
     */
    public function getContentName( $content )
    {
        if( !$content instanceof Content )
        {
            // Load Content
            $content = $this->repository->sudo(
                function( Repository $repository ) use ( $content )
                {
                    /* @var $repository \eZ\Publish\Core\Repository\Repository */
                    return $repository->getContentService()->loadContent( intval( $content ) );
                } );
        }

        if( $content instanceof Content )
        {
            return $this->contentExtension->getTranslatedContentName( $content );
        }

        return "N/A";
    }

    /**
     * Check if current user is the anonymous user defined in ezsettings
     *
     * @return bool
     */
    public function isAnonymousUser()
    {
        // Siteaccess anonymous user ID
        $anonymousUserId = $this->configResolver->getParameter( "anonymous_user_id", "ezsettings" );
        // Current user ID
        $currentUserReference = $this->repository->getPermissionResolver()->getCurrentUserReference();
        if( $currentUserReference instanceof UserReference )
        {
            $currentUserId = $currentUserReference->getUserId();

            return $currentUserId === $anonymousUserId;
        }

        return false;
    }

    public function getName()
    {
        return 'sqli_twig_extension';
    }
}