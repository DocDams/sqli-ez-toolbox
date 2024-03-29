<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\FieldType\SqliNewsLetter;

use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Ibexa\Contracts\Core\FieldType\Generic\Type as GenericType;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use SQLI\EzToolboxBundle\Form\Type\SqliNewsLetter;
use Symfony\Component\Form\FormInterface;
use Ibexa\Contracts\Core\FieldType\Indexable;

final class Type extends GenericType implements FieldValueFormMapperInterface,Indexable
{
    public function getFieldTypeIdentifier(): string
    {
        return 'sqli_news_letter';
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $definition = $data->fieldDefinition;

        $fieldForm->add(
            'value',
            SqliNewsLetter::class,
            [
                'required' => $definition->isRequired,
                'label' => $definition->getName(),
            ]
        );
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
