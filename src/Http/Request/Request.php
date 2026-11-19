<?php

namespace Rivetphp\Http\Request;

/**
 * Handles HTTP request context
 *
 * Base request context class for enabling the transport of a request
 *
 * @package Rivetphp\Http\Request
 * @since v0.1.0
 */
class Request
{
    public string $method;
    public string $uri;
    public string $path;

    public array $params = [];
    public array $query;
    public array $cookies;
    public array $session = [];
    public array $files;

    public mixed $body;
    public string $rawBody;

    public array $headers = [];

    private array $attributes = [];

    /**
     * Builds the request context from the PHP superglobals
     *
     * @since v0.1.0
     */
    public function __construct()
    {
        $this->method = $_SERVER["REQUEST_METHOD"] ?? "GET";
        $this->uri = $_SERVER["REQUEST_URI"] ?? "/";
        $this->path = parse_url($this->uri, PHP_URL_PATH) ?: "/";

        $this->query = $_GET;
        $this->cookies = $_COOKIE;
        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->session = $_SESSION;
        }
        $this->files = $_FILES;

        $this->headers = $this->loadHeaders();

        $this->rawBody = file_get_contents("php://input");
        $this->body = $this->parseBody();
    }

    // HELPERS

    /**
     * Loads all HTTP headers.
     *
     * This function loads HTTP headers, independent of cross platform
     *
     * @return array All headers
     * @since v0.1.0
     */
    private function loadHeaders(): array
    {
        $headers = function_exists("getallheaders") ? getallheaders() : [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, "HTTP_")) {
                $name = str_replace("_", "-", substr($key, 5));
                $headers[$name] ??= $value;
            }
        }

        foreach (["CONTENT_TYPE", "CONTENT_LENGTH"] as $key) {
            if (isset($_SERVER[$key])) {
                $name = str_replace("_", "-", $key);
                $headers[$name] ??= $_SERVER[$key];
            }
        }

        $result = [];

        foreach ($headers as $name => $value) {
            $name = strtolower(str_replace("-", "_", $name));
            $result[$name] = $value;
        }

        return $result;
    }

    /**
     * Decodes the request body according to its content type
     *
     * @return mixed Parsed body, or the raw body when the type is unknown
     * @since v0.1.0
     */
    private function parseBody()
    {
        $content_type = strtolower(
            trim(explode(";", $this->headers["content_type"] ?? "")[0]),
        );

        switch ($content_type) {
            case "application/json":
                return json_decode($this->rawBody, true);

            case "application/x-www-form-urlencoded":
            case "multipart/form-data":
                return $_POST;

            default:
                return $this->rawBody;
        }
    }

    // GETTERS

    /**
     * Returns a stored attribute
     *
     * @param string $_attr_name Attribute name
     * @param mixed $_default Value used when the attribute is missing
     * @return mixed
     * @since v0.1.0
     */
    public function getAttirbute(string $_attr_name, mixed $_default = null)
    {
        if (!array_key_exists($_attr_name, $this->attributes)) {
            return $_default;
        }
        return $this->attributes[$_attr_name];
    }

    /**
     * Returns a matched route parameter
     *
     * @param string $_param_name Parameter name
     * @param mixed $_default Value used when the parameter is missing
     * @return mixed
     * @since v0.1.0
     */
    public function getParam(string $_param_name, mixed $_default = null)
    {
        if (!array_key_exists($_param_name, $this->params)) {
            return $_default;
        }
        return $this->params[$_param_name];
    }

    /**
     * Returns a request header by its normalized name
     *
     * @param string $_header_name Header name, lowercase with underscores
     * @param mixed $_default Value used when the header is missing
     * @return mixed
     * @since v0.1.0
     */
    public function getHeader(string $_header_name, mixed $_default = null)
    {
        if (!array_key_exists($_header_name, $this->headers)) {
            return $_default;
        }
        return $this->headers[$_header_name];
    }

    // SETTERS

    /**
     * Stores an attribute on the request
     *
     * @param string $_key Attribute name
     * @param mixed $_value Attribute value
     * @return void
     * @since v0.1.0
     */
    public function setAttirbute(string $_key, mixed $_value)
    {
        $this->attributes[$_key] = $_value;
    }
}
