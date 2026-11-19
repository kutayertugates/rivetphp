<?php

namespace Rivetphp\Exceptions\Http;

/**
 * Raised when the requested resource does not exist
 *
 * @package Rivetphp\Exceptions\Http
 * @since v0.1.0
 */
class NotFoundException extends \Exception
{
    /**
     * Creates a 404 Not Found exception
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param \Throwable|null $previous Previous exception, if any
     * @since v0.1.0
     */
    public function __construct(
        string $message = "",
        int $code = 404,
        \Throwable|null $previous = null,
    ) {
        return parent::__construct($message, $code, $previous);
    }
}
