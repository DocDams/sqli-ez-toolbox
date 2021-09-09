<?php


namespace SQLI\EzToolboxBundle\Services;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\Event\FieldTypeService;
use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ExtractHelper
{
    protected $contentTypeService;
    protected $fieldTypeService;

    /**
     * ExtractHelper constructor.
     * @param UserService $userService
     * @param PermissionResolver $permissionResolver
     */
    public function __construct(ContentTypeService $contentTypeService,FieldTypeService $fieldTypeService)
    {
        $this->contentTypeService = $contentTypeService;
        $this->fieldTypeService = $fieldTypeService;
    }


    public function createContentToExport($contentTypeIdentifiers, $output = null)
    {
        $contentTypesYaml = "";
        foreach ($contentTypeIdentifiers as $ct) {
            $contentTypeInfosYaml = "";
            if( is_numeric($ct)){
                $contentType = $this->contentTypeService->loadContentType($ct);
            } else {
                $contentType = $this->contentTypeService->loadContentTypeByIdentifier($ct);
            }

            $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups();
            $contentTypeGroup = $contentTypeGroups[0];

            dump($contentType);

            $contentTypeToArray = [];
            //Add Content-Type info
            $contentTypeToArray['type'] = 'content_type';
            $contentTypeToArray['mode'] = 'create';
            $contentTypeToArray['content_type_group'] = $contentTypeGroup->identifier;
            $contentTypeToArray['identifier'] = $contentType->identifier;

            $contentTypeToArray['name'] = $contentType->getNames();
            $contentTypeToArray['name_pattern'] = $contentType->nameSchema;
            $contentTypeToArray['default_always_available'] = $contentType->defaultAlwaysAvailable;
            $descriptions = [];
            foreach ($contentType->getDescriptions() as $descr) {
                if ($descr !== null) $descriptions[] = $descr;
            }
            $contentTypeToArray['description'] = $descriptions;
            if($contentType->isContainer !== null) $contentTypeToArray['isContainer'] = $contentType->isContainer;
            if($contentType->languageCodes !== null) $contentTypeToArray['lang'] = $contentType->languageCodes[0];
            if($contentType->urlAliasSchema !== null) $contentTypeToArray['url_name_pattern'] = $contentType->urlAliasSchema;
            if($contentType->defaultSortField !== null) $contentTypeToArray['default_sort_field'] = $contentType->defaultSortField;
            if($contentType->defaultSortOrder !== null) $contentTypeToArray['default_sort_order'] = $contentType->defaultSortOrder;

            //Add attributes
            $contentTypeFieldDefinitions = $contentType->fieldDefinitions->toArray();
            $arrayToYaml = [];
            $arrayToYaml[] = $contentTypeToArray;

            $attributeToYaml = [];
            foreach ($contentTypeFieldDefinitions as $attribute) {

                $attributeArray = [];
                $attributeArray['type'] =  $attribute->fieldTypeIdentifier;
                $attributeArray['name'] = $attribute->getNames();
                $attributeArray['identifier'] =  $attribute->identifier;
                $descriptions = [];
                foreach ($attribute->getDescriptions() as $descr) {
                    if ($descr !== null) $descriptions[] = $descr;
                }
                $attributeArray['description'] = $descriptions;
                if ($attribute->isRequired !== null) $attributeArray['required'] = $attribute->isRequired;
                if ($attribute->isSearchable!== null) $attributeArray['searchable'] =  $attribute->isSearchable;
                if ($attribute->isInfoCollector !== null) $attributeArray['infoCollector'] =  $attribute->isInfoCollector;
                if ($attribute->isRequired !== null) $attributeArray['disableTranslation'] =  $attribute->isTranslatable;
                if ($attribute->defaultValue !== null) $attributeArray['default-value'] =  $attribute->defaultValue;
                $attributeArray['fieldSettings'] = $attribute->getFieldSettings();
                if ($attribute->position !== null) $attributeArray['position'] =  $attribute->position;

                $attributeToYaml[] = $attributeArray;

            }

            $contentTypeInfosYaml .= Yaml::dump($arrayToYaml);

            $fieldDefinitionsYaml = $this->attributesToYaml($attributeToYaml);
            $contentTypeInfosYaml .= $fieldDefinitionsYaml;
            $contentTypesYaml .= $contentTypeInfosYaml;


        } die();
        return $contentTypesYaml;
    }

    public function attributesToYaml(array $attributeToYaml) {

        $result = "    ";
        $result .= "attributes:\n";

        foreach ($attributeToYaml as $fieldDefinition){
            $result .= "        ";
            $result .= "-\n";
            $result .= "            ";
            $fieldResult = Yaml::dump($fieldDefinition);
            $array = str_split($fieldResult);
            $i = 0;
            $len = count($array);
            foreach ($array as $char){
                $result .= $char;
                if($char == "\n" && $i != $len - 1) {
                    $result .= "            ";
                }
                $i++;
            }
        }
        return $result;
    }
}