<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class InvalidCDNCacheConfigurationException extends Exception
{
    public function __construct($message = 'CDN Cache Configuration invalid', $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}