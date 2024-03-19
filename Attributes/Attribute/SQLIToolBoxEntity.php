<?php

namespace SQLI\EzToolboxBundle\Attributes\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class SQLIToolBoxEntity implements SQLIToolBoxClassAttribute
{
    /** @var int */
    public $max_per_page;

    public function __construct(
        public bool $create = false,
        public bool $update = false,
        public bool $delete = false,
        public string $description = "",
        int $max_per_page = 10,
        public bool $csv_exportable = false,
        public string $tabname = "default"
    ) {
              $this->max_per_page = max(10, $max_per_page);
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
