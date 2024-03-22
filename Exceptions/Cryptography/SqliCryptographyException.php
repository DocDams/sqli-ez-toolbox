<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Exceptions\Cryptography;

use Exception;
use Throwable;

class SqliCryptographyException extends Exception
{
    private mixed $data;

    public function __construct($message = "", $data = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }

    public function hasData(): bool
    {
        return !is_null($this->data);
    }

    public function __toString(): string
    {
        return parent::__toString() . "\nDump: " . $this->dumpData();
    }

    public function dumpData(): bool|string
    {
        return print_r($this->data, true);
    }
}
