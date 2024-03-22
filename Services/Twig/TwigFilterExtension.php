<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Services\Twig;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension;
use SQLI\EzToolboxBundle\Services\DataFormatterHelper;
use SQLI\EzToolboxBundle\Services\FieldHelper;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigFilterExtension extends AbstractExtension
{
    public function __construct(
        private readonly Repository $repository,
        private readonly DataFormatterHelper $dataFormatterHelper,
        private readonly ConfigResolverInterface $configResolver,
        private readonly FieldHelper $fieldHelper,
        private readonly ContentExtension $contentExtension,
        protected AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    public function getFunctions(): array
    {
        return
            [
                new TwigFunction('format_data', $this->dataFormatterHelper->format(...)),
                new TwigFunction('empty_field', $this->isEmptyField(...)),
                new TwigFunction('is_anonymous_user', $this->isAnonymousUser(...)),
                new TwigFunction('content_name', $this->getContentName(...)),
                new TwigFunction('ez_parameter', $this->getParameter(...)),
                new TwigFunction('has_access', $this->hasAccess(...)),
                new TwigFunction('ez_selection_value', $this->fieldHelper->ezselectionSelectedOptionValue(...)),
            ];
    }

    public function getFilters(): array
    {
        return
            [
                new TwigFilter('format_data', $this->dataFormatterHelper->format(...)),
            ];
    }

    /**
     * Search a variable/parameter in eZ namespace
     *
     * @param $parameterName
     * @param $namespace
     * @return mixed|null
     */
    public function getParameter($parameterName, $namespace): mixed
    {
        try {
            return $this->configResolver->getParameter($parameterName, $namespace);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Checks if a given field is considered empty.
     * This method accepts field as Objects or by identifiers.
     *
     * @param string|Field $fieldDefIdentifier Field or Field Identifier to get the value from.
     * @param string|null $forcedLanguage Locale we want the content name translation in (e.g. "fre-FR").
     *                                     Null by default (takes current locale).
     * @return bool
     */
    public function isEmptyField(Content $content, string|Field $fieldDefIdentifier, string $forcedLanguage = null): bool
    {
        return $this->fieldHelper->isEmptyField($content, $fieldDefIdentifier, $forcedLanguage);
    }

    /**
     * Get content name even if current user cannot access to this content
     *
     * @param $content int|Content Content or it's ID
     * @return string
     * @throws InvalidArgumentType
     */
    public function getContentName(Content|int $content): string
    {
        if (!$content instanceof Content) {
            // Load Content
            $content = $this->repository->sudo(
                fn(Repository $repository) =>
                    /* @var $repository \Ibexa\Core\Repository\Repository */
                    $repository->getContentService()->loadContent((int) $content)
            );
        }

        if ($content instanceof Content) {
            return $this->contentExtension->getTranslatedContentName($content);
        }

        return "N/A";
    }

    /**
     * Check if current user is the anonymous user defined in ezsettings
     *
     * @return bool
     */
    public function isAnonymousUser(): bool
    {
        // Siteaccess anonymous user ID
        $anonymousUserId = $this->configResolver->getParameter("anonymous_user_id", "ezsettings");
        // Current user ID
        $currentUserReference = $this->repository->getPermissionResolver()->getCurrentUserReference();

            $currentUserId = $currentUserReference->getUserId();

            return $currentUserId === $anonymousUserId;
    }

    public function hasAccess(string $module, string $function): bool
    {
        return $this->authorizationChecker->isGranted(
            new Attribute($module, $function)
        );
    }

    public function getName(): string
    {
        return 'sqli_twig_extension';
    }
}
