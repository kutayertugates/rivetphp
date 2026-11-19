<?php

namespace Rivetphp\Exceptions\Http;

class UnauthorizedException extends \Exception
{
    public function __construct(
        string $message = "",
        int $code = 401,
        \Throwable|null $previous = null,
    ) {
        return parent::__construct($message, $code, $previous);
    }
}
