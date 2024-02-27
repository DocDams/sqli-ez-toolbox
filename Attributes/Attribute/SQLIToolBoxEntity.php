<?php

namespace SQLI\EzToolboxBundle\Attributes\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class SQLIToolBoxEntity implements SQLIToolBoxClassAttribute
{

    /** @var bool */
    public $create ;

    /** @var bool */
    public $update ;

    /** @var bool */
    public $delete;

    /** @var string */
    public $description;

    /** @var int */
    public $max_per_page;

    /** @var bool */
    public $csv_exportable ;

    /** @var string */
    public $tabname;

    public function __construct(bool $create= false, bool $update= false, bool $delete = false, string $description = "", int $max_per_page = 10, bool $csv_exportable= false, string $tabname = "default")
    {
              $this->create = $create;

              $this->update = $update;

              $this->delete = $delete;

              $this->description = $description;

              $this->max_per_page = max(10, $max_per_page); // Ensure max_per_page is non-negative


              $this->csv_exportable = $csv_exportable;

              $this->tabname = $tabname;
    }

    /**
     * @return bool
     */
    public function isCreate(): bool
    {
        return $this->create;
    }

    /**
     * @return bool
     */
    public function isUpdate(): bool
    {
        return $this->update;
    }

    /**
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->delete;
    }

    /**
     * @return String
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getMaxPerPage(): int
    {
        return $this->max_per_page;
    }

    /**
     * @return bool
     */
    public function isCsvExportable(): bool
    {
        return $this->csv_exportable;
    }

    /**
     * @return String
     */
    public function getTabname(): string
    {
        return $this->tabname;
    }

}