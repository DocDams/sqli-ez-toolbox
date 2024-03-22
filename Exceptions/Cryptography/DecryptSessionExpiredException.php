<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Exceptions\Cryptography;

class DecryptSessionExpiredException extends SqliCryptographyException
{
    public function __construct($data = null, $exception = null)
    {
        parent::__construct('Encrypted message expired', $data, 520, $exception);
    }
}
