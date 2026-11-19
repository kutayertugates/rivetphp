<?php

namespace Rivetphp\Exceptions\Http;

/**
 * Raised for malformed or invalid requests
 *
 * @package Rivetphp\Exceptions\Http
 * @since v0.1.0
 */
class BadRequestException extends \Exception
{
    /**
     * Creates a 400 Bad Request exception
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param \Throwable|null $previous Previous exception, if any
     * @since v0.1.0
     */
    public function __construct(
        string $message = "",
        int $code = 400,
        \Throwable|null $previous = null,
    ) {
        return parent::__construct($message, $code, $previous);
    }
}
