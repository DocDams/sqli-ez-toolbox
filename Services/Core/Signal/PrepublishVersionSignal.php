<?php

namespace SQLI\EzToolboxBundle\Services\Core\Signal;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\Core\SignalSlot\Signal;

class PrepublishVersionSignal extends Signal
{
    /**
     * Content ID.
     *
     * @var mixed
     */
    public $contentId;

    /**
     * Version Number.
     *
     * @var int
     */
    public $versionNo;

    /**
     * List of language codes of translations affected by the given publish operation.
     *
     * The list is taken from the <code>$translations</code> argument of
     * the ContentService::publishVersion API.
     *
     * @see \eZ\Publish\API\Repository\ContentService::publishVersion
     *
     * Other translations were copied from the previously published Version.
     *
     * @var string[]
     */
    public $affectedTranslations = Language::ALL;
}
