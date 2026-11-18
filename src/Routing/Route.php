<?php

namespace Rivetphp\Routing;

class Route
{
    public array $methods = [];
    public string $path = "";
    public $handler;

    public string $pattern;

    public function __construct(
        array $_methods,
        string $_path,
        callable $_handler,
    ) {
        if ($_methods === []) {
            $this->methods[] = "GET";
        } else {
            foreach ($_methods as $m) {
                $this->methods[] = strtoupper($m);
            }
        }
        $this->path = $_path;
        $this->handler = $_handler;
        $this->pattern = $this->generatePattern();
    }

    private function generatePattern(): string
    {
        $segments = array_filter(
            explode("/", trim($this->path, "/")),
            static fn(string $segment): bool => $segment !== "",
        );

        $pattern = "";

        foreach ($segments as $segment) {
            if (
                !preg_match(
                    "#^\{([a-zA-Z_][a-zA-Z0-9_]*)(\?)?\}$#",
                    $segment,
                    $m,
                )
            ) {
                // Düz metin parçası: sadece burada kaçışla.
                $pattern .= "/" . preg_quote($segment, "#");
                continue;
            }

            $group = "(?P<" . $m[1] . ">[^/]+)";

            $pattern .=
                ($m[2] ?? "") === "?" ? "(?:/" . $group . ")?" : "/" . $group;
        }

        return "#^" . $pattern . "/?$#";
    }
}
