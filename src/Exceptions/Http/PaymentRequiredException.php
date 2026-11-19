<?php

namespace Rivetphp\Exceptions\Http;

/**
 * Raised when payment is required to access the resource
 *
 * @package Rivetphp\Exceptions\Http
 * @since v0.1.0
 */
class PaymentRequiredException extends \Exception
{
    /**
     * Creates a 402 Payment Required exception
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param \Throwable|null $previous Previous exception, if any
     * @since v0.1.0
     */
    public function __construct(
        string $message = "",
        int $code = 402,
        \Throwable|null $previous = null,
    ) {
        return parent::__construct($message, $code, $previous);
    }
}
