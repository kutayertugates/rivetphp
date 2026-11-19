<?php

namespace Rivetphp\Exceptions\Http;

class RequestTimeoutException extends \Exception
{
    public function __construct(
        string $message = "",
        int $code = 408,
        \Throwable|null $previous = null,
    ) {
        return parent::__construct($message, $code, $previous);
    }
}
