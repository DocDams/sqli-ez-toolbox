<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Services;

use SQLI\EzToolboxBundle\Classes\Filter;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FilterEntityHelper
{
    public const SESSION_VARNAME = "sqli_admin_filter_fqcn";

    public function __construct(private readonly SessionInterface $session)
    {
    }

    /**
     * Save Filter object in session for specified FQCN
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
     * @return Filter|null
     */
    public function getFilter(string $fqcn): ?Filter
    {
        // Get from session
        $filters = $this->session->get(self::SESSION_VARNAME, []);

        return $filters[$fqcn] ?? null;
    }

    public function resetFilter(string $fqcn): void
    {
        $filters = $this->session->get(self::SESSION_VARNAME, []);
        unset($filters[$fqcn]);

        $this->session->set(self::SESSION_VARNAME, $filters);
    }
}
