<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Services;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Selection\Value;
use Ibexa\Core\Helper\FieldHelper as EzFieldHelper;
use Ibexa\Core\Helper\TranslationHelper;

class FieldHelper
{
    public function __construct(private readonly EzFieldHelper $fieldHelper, private readonly TranslationHelper $translationHelper)
    {
    }

    /**
     * @param string|Field $fieldDefIdentifier Field or Field Identifier to get the value from.
     * @param null $forcedLanguage Locale we want the content name translation in (e.g. "fre-FR").
     *                                     Null by default (takes current locale).
     *
     * @return bool
     * @see \Ibexa\Core\Helper\FieldHelper
     *
     * Checks if a given field is considered empty.
     * This method accepts field as Objects or by identifiers.
     */
    public function isEmptyField(Content $content, string|Field $fieldDefIdentifier, $forcedLanguage = null): bool
    {
        if ($fieldDefIdentifier instanceof Field) {
            $fieldDefIdentifier = $fieldDefIdentifier->fieldDefIdentifier;
        }

        // Check if field exist in content type definition
        if (!$content->getContentType()->getFieldDefinition($fieldDefIdentifier) instanceof FieldDefinition) {
            return true;
        }

        // Field exists, check if value is empty
        return $this->fieldHelper->isFieldEmpty($content, $fieldDefIdentifier, $forcedLanguage);
    }


    /**
     * Return value of the selected option for an attribute 'ezselection'
     *
     * @param null $forcedLanguage
     * @return string|null
     */
    public function ezselectionSelectedOptionValue(
        Content $content,
        string $fieldDefIdentifier,
        $forcedLanguage = null
    ): ?string {
        $fieldDefinition = $content->getContentType()->getFieldDefinition($fieldDefIdentifier);
        if ($fieldDefinition->fieldTypeIdentifier == "ezselection") {
            $fieldValue = $content->getFieldValue($fieldDefIdentifier, $forcedLanguage);
            if ($fieldValue instanceof Value) {
                $selectedValue = $fieldValue->selection;
                $selectedValue = reset($selectedValue);

                if ($selectedValue !== false) {
                    // Search selected value in field definition
                    $fieldSettings = $fieldDefinition->getFieldSettings();
                    if (array_key_exists('options', $fieldSettings)) {
                        $selectionDefinition = $fieldSettings['options'];

                        if (array_key_exists($selectedValue, $selectionDefinition)) {
                            return $selectionDefinition[$selectedValue];
                        }
                    }
                }
            }
        }

        return null;
    }
}
