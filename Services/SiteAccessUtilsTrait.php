<?php

namespace SQLI\EzToolboxBundle\Services;

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

trait SiteAccessUtilsTrait
{
    /** @var SiteAccess */
    protected $siteAccess;
    /** @var array */
    protected $siteaccessAdminGroup;

    /**
     * autowiring
     *
     * @required
     * @param SiteAccess $siteAccess
     * @param ParameterBagInterface $parameterBag
     */
    public function setSiteAccessSettings(SiteAccess $siteAccess, ParameterBagInterface $parameterBag): void
    {
        $this->siteAccess = $siteAccess;
        $this->siteaccessAdminGroup = [];
        if ($parameterBag->has('ezpublish.siteaccess.groups')) {
            $siteaccessAdminGroup = $parameterBag->get('ezpublish.siteaccess.groups');
            $this->siteaccessAdminGroup = $siteaccessAdminGroup['admin_group'];
        }
    }

    /**
     * Check if specified (or current if null) siteaccess name is in admin group
     *
     * @param string|null $siteaccess
     * @return bool
     */
    public function isAdminSiteAccess(?string $siteaccess = null): bool
    {
        if (is_null($siteaccess)) {
            $siteaccess = $this->getSiteAccessName();
        }

        return in_array($siteaccess, $this->siteaccessAdminGroup);
    }

    /**
     * @return string
     */
    public function getSiteAccessName(): string
    {
        return $this->siteAccess->name;
    }
}
