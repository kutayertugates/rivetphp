<?php

namespace Rivetphp\Exceptions\Http;

class PaymentRequiredException extends \Exception
{
    public function __construct(
        string $message = "",
        int $code = 402,
        \Throwable|null $previous = null,
    ) {
        return parent::__construct($message, $code, $previous);
    }
}
