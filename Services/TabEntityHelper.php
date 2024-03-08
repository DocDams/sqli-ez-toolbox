<?php

namespace SQLI\EzToolboxBundle\Services;

use ReflectionException;

class TabEntityHelper
{
    public function __construct(private readonly EntityHelper $entityHelper)
    {
    }

    /**
     * Prepare array with tabname as key and array with classes in this tab
     * @return array
     * @throws ReflectionException
     */
    public function entitiesGroupedByTab(): array
    {
        // Sorted classes by tabname
        $tabsEntities = ['default' => null];

        // Annotated classes
        $annotatedClasses = $this->entityHelper->getAnnotatedClasses();
        foreach ($annotatedClasses as $fqcn => $annotatedClass) {
            // Get tabname
            $tabname = $annotatedClass['annotation']->getTabname();
            // Add class under tab
            $tabsEntities[$tabname][$fqcn] = $annotatedClass;
        }

        return $tabsEntities;
    }
}
