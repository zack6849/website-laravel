<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class CachePurgeFailureException extends Exception
{
    public function __construct(
        string $message = "Failed to purge cache",
        int $code = 500,
        \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
