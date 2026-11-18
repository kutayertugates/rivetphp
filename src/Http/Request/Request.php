<?php

namespace Rivetphp\Http\Request;

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

    private function loadHeaders(): array
    {
        $headers = function_exists("getallheaders") ? getallheaders() : [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, "HTTP_")) {
                $name = str_replace("_", "-", substr($key, 5));
                $headers[$name] ??= $value;
            }
        }

        // Content-Type ve Content-Length HTTP_ prefix'i olmadan gelebilir.
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

    public function getAttirbute(string $_attr_name, mixed $_default = null)
    {
        if (!array_key_exists($_attr_name, $this->attributes)) {
            return $_default;
        }
        return $this->headers[$_attr_name];
    }

    public function getParam(string $_param_name, mixed $_default = null)
    {
        if (!array_key_exists($_param_name, $this->params)) {
            return $_default;
        }
        return $this->params[$_param_name];
    }

    public function getHeader(string $_header_name, mixed $_default = null)
    {
        if (!array_key_exists($_header_name, $this->headers)) {
            return $_default;
        }
        return $this->headers[$_header_name];
    }

    // SETTERS

    public function setAttirbute(string $_key, mixed $_value)
    {
        $this->attributes[$_key] = $_value;
    }
}
