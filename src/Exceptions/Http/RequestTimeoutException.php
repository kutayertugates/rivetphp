<?php

namespace Rivetphp\Exceptions\Http;

/**
 * Raised when the client takes too long to complete the request
 *
 * @package Rivetphp\Exceptions\Http
 * @since v0.1.0
 */
class RequestTimeoutException extends \Exception
{
    /**
     * Creates a 408 Request Timeout exception
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param \Throwable|null $previous Previous exception, if any
     * @since v0.1.0
     */
    public function __construct(
        string $message = "",
        int $code = 408,
        \Throwable|null $previous = null,
    ) {
        return parent::__construct($message, $code, $previous);
    }
}
