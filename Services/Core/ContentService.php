<?php

namespace SQLI\EzToolboxBundle\Services\Core;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\SignalSlot\ContentService as ContentServiceBase;
use SQLI\EzToolboxBundle\Services\Core\Signal\PrepublishVersionSignal;

class ContentService extends ContentServiceBase
{
    public function publishVersion(VersionInfo $versionInfo, array $translations = Language::ALL)
    {
        $this->signalDispatcher->emit(
            new PrepublishVersionSignal(
                [
                    'contentId' => $versionInfo->getContentInfo()->id,
                    'versionNo' => $versionInfo->versionNo,
                    'affectedTranslations' => $translations,
                ]
            )
        );

        return parent::publishVersion($versionInfo, $translations);
    }
}
