<?php

namespace SQLI\EzToolboxBundle\Attributes;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use ReflectionClass;
use ReflectionException;
use SQLI\EzToolboxBundle\Attributes\Attribute\SQLIToolBoxEntity;
use SQLI\EzToolboxBundle\Attributes\Attribute\SQLIToolBoxEntityProperty;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class SQLIAttributesManager
{
    public function __construct(
        /**
         * Classname of annotation
         */
        private readonly string $attributeClassName,
        private readonly array $directories,
        /**
         * Project root directory
         */
        private readonly string $projectDir
    ) {
    }


    /**
     * Returns all PHP classes annotated with attribute specified in service declaration (see services.yml)
     * @return array
     * @throws ReflectionException
     *
     */
    public function getAttributedClasses(): array
    {
        $attributes = $this->getSQLIAttributes();

        // Only annotation in service declaration will be kept
        if (array_key_exists($this->attributeClassName, $attributes)) {
            return $attributes[$this->attributeClassName];
        }

        return [];
    }


    /**
     * Returns all PHP classes annotated with the specified attribute class.
     *
     * @return array
     * @throws ReflectionException
     */
    public function getSQLIAttributes(): array
    {
        // Initialize an empty array to store annotated classes
        $annotatedClasses = [];

        // Iterate over each directory specified in the configuration
        foreach ($this->directories as $entitiesMapping) {
            $directory = $entitiesMapping['directory'];
            $namespace = $entitiesMapping['namespace'] ?? str_replace('/', '\\', (string) $directory);

            // Construct the full path to the directory
            $path = $this->projectDir . '/src/' . $directory;

            // Create a Symfony Finder instance to search for PHP files in the directory
            $finder = new Finder();
            $finder->depth(0)->files()->in($path);
            $annotatedClasses = $this->getAttributedClassesArray($finder, $namespace, $annotatedClasses);
        }
        return $annotatedClasses;
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function getAttributedClassesArray(Finder $finder, mixed $namespace, array $annotatedClasses): array
    {
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            // Get the base name of the PHP file (class name)
            $className = $file->getBasename('.php');
            $classNamespace = "$namespace\\$className";
            // Create a reflection class from the generated namespace to read attributes
            $class = new ReflectionClass($classNamespace);

            // Search if $class uses an SQLIToolBoxClassEntity
            $classAttribute = null;
            foreach ($class->getAttributes() as $attribute) {
                if ($attribute->getName() === SQLIToolBoxEntity::class) {
                    $classAttributeArguments = $attribute->getArguments();

                    $classAttribute = new SQLIToolBoxEntity(
                        create: $classAttributeArguments['create'] ?? false,
                        update: $classAttributeArguments['update'] ?? false,
                        delete: $classAttributeArguments['delete'] ?? false,
                        description: $classAttributeArguments['description'] ?? "",
                        max_per_page: $classAttributeArguments['max_per_page'] ?? 10,
                        csv_exportable: $classAttributeArguments['csv_exportable'] ?? false,
                        tabname: $classAttributeArguments['tabname'] ?? "default",
                    );
                    break;
                }
            }

            // Check if $class use Doctrine\Entity Attribute
            $classDoctrineAttribute = null;
            foreach ($class->getAttributes() as $attribute) {
                if ($attribute->getName() === Entity::class) {
                    $classDoctrineAttribute = Entity::class;
                    break;
                }
            }

            if (!$classAttribute instanceof SQLIToolBoxEntity && $classDoctrineAttribute === null) {
                // No EntityAnnotationInterface or isn't an entity, ignore her
                continue;
            }

            // Prepare properties
            $properties = [];
            $compoundPrimaryKey = [];

            $reflectionProperties = $class->getProperties();

            // phpcs:ignore
            [$annotatedClasses] = $this->getAttributedProperties($reflectionProperties, $properties, $compoundPrimaryKey, $className, $classAttribute, $annotatedClasses, $classNamespace);
        }
        return $annotatedClasses;
    }

    /**
     * @param SQLIToolBoxEntity|null $classAttribute
     * @return array
     */
    // phpcs:ignore
    public function getAttributedProperties(array $reflectionProperties, array $properties, array $compoundPrimaryKey, string $className, ?SQLIToolBoxEntity $classAttribute, array $annotatedClasses, string $classNamespace): array
    {
        foreach ($reflectionProperties as $reflectionProperty) {
            // Accessibility of each property
            $accessibility = "public"; // public
            if ($reflectionProperty->isPrivate()) {
                $accessibility = "private"; // private
            } elseif ($reflectionProperty->isProtected()) {
                $accessibility = "protected"; // protected
            }

            // Try to get an SQLIPropertyAttribute
            $visible = true;
            $readonly = false;
            $required = true;
            $columnType = "string";
            $description = null;
            $choices = null;
            $extraLink = null;

            foreach ($reflectionProperty->getAttributes() as $attribute) {
                // Récupérer l'instance de l'attribut
                $attributeInstance = $attribute->newInstance();

                // Vérifier si l'attribut est une instance de SQLIToolBoxClassProperty
                if ($attributeInstance instanceof SQLIToolBoxEntityProperty) {
                    // Check if a visibility information defined on entity's property with 'visible' attribute
                    $visible = $attributeInstance->isVisible();
                    // Check if property must be only in readonly
                    $readonly = $attributeInstance->isReadonly();
                    // Get property description
                    $description = $attributeInstance->getDescription();
                    // Get choices
                    $choices = $attributeInstance->getChoices();
                    $extraLink = $attributeInstance->getExtraLink();
                }

                if ($attributeInstance instanceof Column) {
                    $columnType = $attributeInstance->type;

                    $required = !$attributeInstance->nullable;
                }

                $properties[$reflectionProperty->getName()] = [
                    'accessibility' => $accessibility,
                    'visible' => $visible,
                    'readonly' => $readonly,
                    'required' => $required,
                    'type' => $columnType,
                    'description' => $description,
                    'choices' => $choices,
                    'extra_link' => $extraLink,
                ];

                //Get The Primary Keys
                if ($attributeInstance instanceof Id) {
                    $compoundPrimaryKey[] = $reflectionProperty->getName();
                }
            }


            $annotatedClasses[$this->attributeClassName][$classNamespace] =
                [
                    'classname' => $className,
                    'annotation' => $classAttribute,
                    'properties' => $properties,
                    'primary_key' => $compoundPrimaryKey,
                ];
        }
        return array($annotatedClasses);
    }
}
