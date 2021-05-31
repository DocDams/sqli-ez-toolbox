<?php

namespace SQLI\EzToolboxBundle\Services;

use SQLI\EzToolboxBundle\Classes\Filter;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FilterEntityHelper
{
    public const SESSION_VARNAME = "sqli_admin_filter_fqcn";

    /** @var SessionInterface */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Save Filter object in session for specified FQCN
     *
     * @param string $fqcn
     * @param Filter $filter
     */
    public function setFilter(string $fqcn, Filter $filter): void
    {
        // Set in session
        $filters = $this->session->get(self::SESSION_VARNAME, []);
        $filters[$fqcn] = $filter;
        $this->session->set(self::SESSION_VARNAME, $filters);
    }

    /**
     * Get Filter object from session for specified FQCN
     *
     * @param string $fqcn
     * @return Filter|null
     */
    public function getFilter(string $fqcn): ?Filter
    {
        // Get from session
        $filters = $this->session->get(self::SESSION_VARNAME, []);

        return array_key_exists($fqcn, $filters) ? $filters[$fqcn] : null;
    }

    /**
     * @param string $fqcn
     */
    public function resetFilter(string $fqcn): void
    {
        $filters = $this->session->get(self::SESSION_VARNAME, []);
        unset($filters[$fqcn]);

        $this->session->set(self::SESSION_VARNAME, $filters);
    }
}
