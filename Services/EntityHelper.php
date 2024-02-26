<?php

namespace SQLI\EzToolboxBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ReflectionException;
use SQLI\EzToolboxBundle\Annotations\SQLIAnnotationManager;
use SQLI\EzToolboxBundle\Attributes\SQLIAttributesManager;
use SQLI\EzToolboxBundle\Classes\Filter;
use Symfony\Component\DependencyInjection\ContainerInterface;
class EntityHelper
{
    const ANNOTATION = "annotation";
    const ATTRIBUTE = "attribute";

    public function __construct(
        private EntityManagerInterface $entityManager,
        private SQLIAttributesManager $attributesManager,
        private SQLIAnnotationManager $annotationManager,
        private FilterEntityHelper $filterEntityHelper,
        private ContainerInterface $container,
    ) {
    }

    /**
     * Get the annotation mapping type from the configuration file
     *
     */
    public function getMappingType(): string
    {
        // Access the parameter from the container
        return $this->container->getParameter('sqli_ez_toolbox.mapping.type');
    }

    /**
     * Get an entity with her information and elements
     *
     * @param string $fqcn
     * @param bool $fetchElements
     * @param bool|array $sort Array( 'column_name' => '', 'order' => 'ASC|DESC' )
     * @return mixed
     * @throws ReflectionException
     */
    public function getEntity(string $fqcn, $fetchElements = true, $sort = false): array
    {
        $annotatedClass['fqcn'] = $fqcn;
        $annotatedClass['class'] = $this->getAnnotatedClass($fqcn);

        if ($fetchElements) {
            // Prepare a filter (only properties flagged as visible or without this annotation) for findAll
            $filteredColums = [];
            foreach ($annotatedClass['class']['properties'] as $propertyName => $propertyInfos) {
                if ($propertyInfos['visible']) {
                    $filteredColums[] = $propertyName;
                }
            }

            // Get filter in session if exists
            $filter = $this->filterEntityHelper->getFilter($fqcn);

            // Get all elements
            $annotatedClass['elements'] = $this->findAll($fqcn, $filteredColums, $filter, $sort);
        }

        return $annotatedClass;
    }

    /**
     * Get a class annotated with SQLIClassAnnotation interface from her FQCN
     *
     * @param string $fqcn
     * @return array
     * @throws ReflectionException
     */
    public function getAnnotatedClass(string $fqcn): ?array
    {
        $annotatedClasses = $this->getAnnotatedClasses();

        return array_key_exists($fqcn, $annotatedClasses) ? $annotatedClasses[$fqcn] : null;
    }

    /**
     * Get all classes annotated with SQLIClassAnnotation interface
     *
     * @return array
     * @throws ReflectionException
     */
    public function getAnnotatedClasses(): array
    {
        $mapping_type=$this->getMappingType();

        if( $mapping_type !== self::ANNOTATION && $mapping_type !== self::ATTRIBUTE)
            throw new \UnexpectedValueException("Unexpected mapping type '{$mapping_type}'. EntityHelper expects either 'annotation' or 'attribute'. Please review your SQLIToolBox configuration file.");


        if($mapping_type== self::ANNOTATION){
            $annotatedClasses = $this->annotationManager->getAnnotatedClasses();
        }else {
            $annotatedClasses = $this->attributesManager->getAttributedClasses();
        }

        foreach ($annotatedClasses as $annotatedFQCN => &$annotatedClass) {
            $annotatedClass['count'] = $this->count($annotatedFQCN);
        }

        return $annotatedClasses;
    }

    /**
     * Count number of element for an entity
     * @param string $entityClass FQCN
     * @return int
     */
    public function count(string $entityClass): int
    {
        return $this->entityManager->getRepository($entityClass)->count([]);
    }

    /**
     * Retrieve all lines in SQL table
     *
     * @param string $entityClass FQCN
     * @param array|null $filteredColums
     * @param Filter|null $filter
     * @param bool|array $sort Array( 'column_name' => '', 'order' => 'ASC|DESC' )
     * @return array
     */
    public function findAll(string $entityClass, $filteredColums = null, $filter = null, $sort = false): array
    {
        /** @var $repository EntityRepository */
        $repository = $this->entityManager->getRepository($entityClass);
        $queryBuilder = $repository->createQueryBuilder('entity');

        // In case of filtering columns
        if (is_array($filteredColums)) {
            array_walk($filteredColums, function (&$columnName) {
                $columnName = "entity.$columnName";
            });
            $select = implode(",", $filteredColums);

            // Change SELECT clause
            $queryBuilder->select($select);
        }

        // Filter
        if (!is_null($filter)) {
            // Add clause 'where'
            $queryBuilder->andWhere(sprintf(
                "entity.%s %s :value",
                $filter->getColumnName(),
                array_search($filter->getOperand(), Filter::OPERANDS_MAPPING)
            ));

            $value = $filter->getValue();

            // Add % around value if operand is LIKE or NOT LIKE
            if (stripos($filter->getOperand(), 'LIKE') !== false) {
                $value = "%" . $value . "%";
            }

            // Bind parameter
            $queryBuilder->setParameter('value', $value);
        }

        // Sort
        if ($sort !== false) {
            $queryBuilder->orderBy("entity." . $sort['column_name'], ($sort['order'] == "ASC" ? "ASC" : "DESC"));
        }

        // Return results as array (ignore accessibility of properties)
        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * Remove an element
     * $findCriteria = ['columnName' => 'value']
     *
     * @param string $entityClass FQCN
     * @param array $findCriteria
     */
    public function remove(string $entityClass, array $findCriteria): void
    {
        $element = $this->findOneBy($entityClass, $findCriteria);
        if (!is_null($element)) {
            $this->entityManager->remove($element);
            $this->entityManager->flush();
        }
    }

    /**
     * Find one element
     *
     * @param string $entityClass
     * @param array $findCriteria
     * @return object|null
     */
    public function findOneBy(string $entityClass, array $findCriteria)
    {
        return $this->entityManager->getRepository($entityClass)->findOneBy($findCriteria);
    }

    /**
     * @param $object
     * @param string $property_name
     * @return false|string
     */
    public function attributeValue($object, string $property_name)
    {
        if ($object[$property_name] instanceof \DateTime) {
            // Datetime doesn't have a __toString method
            return date_format($object[$property_name], "c");
        } elseif ($object[$property_name] instanceof \stdClass) {
            return (serialize($object[$property_name]));
        }
        else {
            return strval($object[$property_name]);
        }
    }

}
