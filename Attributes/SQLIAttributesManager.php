<?php

namespace SQLI\EzToolboxBundle\Attributes;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use ReflectionClass;
use ReflectionException;
use SQLI\EzToolboxBundle\Attributes\Attribute\SQLIToolBoxClassProperty;
use SQLI\EzToolboxBundle\Attributes\Attribute\SQLIToolBoxEntity;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class SQLIAttributesManager
{

    /**
     * Classname of annotation
     * @var string
     */
    private string $attributeClassName;

    /** @var array */
    private array $directories;

    /**
     * Project root directory
     * @var string
     */
    private string $projectDir;

    public function __construct(string $attributeClassName, array $directories, string $projectDir)
    {
        // Store the attribute class name, directories, and project directory
        $this->attributeClassName = $attributeClassName;
        $this->directories = $directories;
        $this->projectDir = $projectDir;
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
            $namespace = $entitiesMapping['namespace'] ?? str_replace('/', '\\', $directory);

            // Construct the full path to the directory
            $path = $this->projectDir . '/src/' . $directory;

            // Create a Symfony Finder instance to search for PHP files in the directory
            $finder = new Finder();
            $finder->depth(0)->files()->in($path);

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
                        $classAttributeArguments=$attribute->getArguments();

                        // TODO Find a solution to get the class object with default values
                        $classAttribute = new SQLIToolBoxEntity(
                            create: $classAttributeArguments['create'],
                            update: $classAttributeArguments['update'],
                            delete: $classAttributeArguments['delete'],
                            description: $classAttributeArguments['description'],
                            tabname: $classAttributeArguments['tabname'],
                        );
                        break;
                    }
                }

                // Check if $class use Doctrine\Entity Attribute
                $classDoctrineAttribute=null;
                foreach ($class->getAttributes() as $attribute) {
                    if ($attribute->getName() === Entity::class) {
                        $classDoctrineAttribute = Entity::class;
                        break;
                    }
                }

                if ($classAttribute===null && $classDoctrineAttribute===null) {
                    // No SQLIClassAnnotation or isn't an entity, ignore her
                    continue;
                }

                // Prepare properties
                $properties = [];
                $compoundPrimaryKey = [];

                $reflectionProperties = $class->getProperties();

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
                        if ($attributeInstance instanceof SQLIToolBoxClassProperty) {
                            // Check if a visibility information defined on entity's property thanks to 'visible' attribute
                            $visible = $attributeInstance->isVisible();
                            // Check if property must be only in readonly
                            $readonly = $attributeInstance->isReadonly();
                            // Get property description
                            $description = $attributeInstance->getDescription();
                            // Get choices
                            $choices = $attributeInstance->getChoices();
                            $extraLink = $attributeInstance->getExtraLink();


                        }

                        if ( $attributeInstance instanceof Column){
                            $columnType = $attributeInstance->type;
                            //  $required = $columnType == "boolean" ? false : !boolval($nullablePropertyAnnotation->nullable);
                            // TODO Verify this logic in the main bundle
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
                         if ( $attributeInstance instanceof Id){
                             $compoundPrimaryKey[] = $reflectionProperty->getName();
                         }
                    }


                    // TODO Vérifier si on change clé $attributeClassname et attribute
                    $annotatedClasses[$this->attributeClassName][$classNamespace] =
                        [
                            'classname' => $className,
                            'annotation' => $classAttribute ,
                            'properties' => $properties,
                            'primary_key' => $compoundPrimaryKey,
                        ];
                }
            }
        }
        return $annotatedClasses;
    }

}
