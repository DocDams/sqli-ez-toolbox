<?php
declare(strict_types=1);

namespace App\Services\FieldType\SelectionFromEntity;


use App\Form\Type\SelectionFromEntity;
use App\Form\Type\SelectionFromEntitySettingsType;
use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Ibexa\Contracts\Core\FieldType\Generic\Type as GenercType;
use Symfony\Component\Form\FormInterface;
use Ibexa\AdminUi\FieldType\FieldDefinitionFormMapperInterface;

final class Type extends GenercType implements FieldValueFormMapperInterface, FieldDefinitionFormMapperInterface
{
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
        ];
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $definition = $data->fieldDefinition;
        $fieldForm->add('value', SelectionFromEntity::class, [
            'required' => $definition->isRequired,
            'label' => $definition->getName(),
            'data' => $data->fieldDefinition->getFieldSettings(),

        ]);
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $fieldDefinitionForm->add('fieldSettings', SelectionFromEntitySettingsType::class, [
            'label' => false,
        ]);
    }
}
