<?php

namespace App\Exceptions;

use Exception;

class HttpException extends Exception
{
    private int $statusCode;
    private mixed $error;

    public function __construct(int $statusCode, string $message, $error = null)
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->error = $error;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getError(): mixed
    {
        return $this->error;
    }
}
