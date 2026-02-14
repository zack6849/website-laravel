<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class FileCannotBeDeletedException extends Exception
{
    public function __construct(
        string $message = "File cannot be deleted",
        int $code = 422,
        \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
