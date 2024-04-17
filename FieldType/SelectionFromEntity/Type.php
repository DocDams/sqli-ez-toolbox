<?php
declare(strict_types=1);

namespace  SQLI\EzToolboxBundle\FieldType\SelectionFromEntity;



use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Ibexa\Contracts\Core\FieldType\Generic\Type as GenericType;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use SQLI\EzToolboxBundle\Annotations\SQLIAnnotationManager;
use SQLI\EzToolboxBundle\Attributes\SQLIAttributesManager;
use SQLI\EzToolboxBundle\Form\Type\SelectionFromEntity;
use SQLI\EzToolboxBundle\Form\Type\SelectionFromEntitySettingsType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Ibexa\AdminUi\FieldType\FieldDefinitionFormMapperInterface;
use Ibexa\Contracts\Core\FieldType\Indexable;

final class Type extends GenericType implements FieldValueFormMapperInterface, FieldDefinitionFormMapperInterface,Indexable
{
    private const ANNOTATION = "annotation";


    public function __construct(
        private ContainerInterface $container,
        private SQLIAttributesManager $attributesManager,
        private SQLIAnnotationManager $annotationManager
    ) {
    }

    public function getMappingType(): string
    {
        // Access the parameter from the container
        return $this->container->getParameter('sqli_ez_toolbox.mapping.type');
    }
    public function getFieldTypeIdentifier(): string
    {
        return 'selection_from_entity';
    }
    public function getSettingsSchema(): array
    {

        return [
            'className' => [
                'type' => 'string',
                'default' => 'App\Entity\Doctrine',
            ],
            'labelAttribute' => [
                'type' => 'string',
            ],
            'valueAttribute' => [
                'type' => 'string',
            ],
            'filter' => [
                'type' => 'string',
            ]
        ];

    }
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $definition = $data->fieldDefinition;

        $mapping_type = $this->getMappingType();
        if ($mapping_type == self::ANNOTATION) {
            $entities = $this->annotationManager->getAnnotatedClasses();
        } else {
            $entities = $this->attributesManager->getAttributedClasses();
        }

        foreach ($entities as $key => $value) {
            $choices[$key] = $value["classname"];
        }
        if(in_array($definition->fieldSettings['className'],$choices)) {
            $classPath= array_search($definition->fieldSettings['className'], $choices);
        }


        $fieldForm->add(
            'value',
            SelectionFromEntity::class,
            array(
                'required' => $definition->isRequired,
                'label' => $definition->getName(),
                'empty_data' => $classPath,
                'data' => $data->fieldDefinition->getFieldSettings(),
            )
        );
    }
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {

        $fieldDefinitionForm->add('fieldSettings', SelectionFromEntitySettingsType::class, [
            'label' => false,
        ]);
    }

    public function getIndexData(Field $field, FieldDefinition $fieldDefinition)
    {
        return [];
    }

    public function getIndexDefinition()
    {
        return [];
    }

    public function getDefaultMatchField()
    {
        return null;
    }

    public function getDefaultSortField()
    {
        return null;
    }
}
