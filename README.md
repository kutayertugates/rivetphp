# RivetPHP

RivetPHP is a small HTTP framework written in plain PHP. You register a few routes, return a response, and that is the whole story. No container to wire up, no service provider to boot, no config tree to learn before the first request. Composer's autoloader, the incoming request, and your code.

It was built for the kind of PHP that has to run anywhere: shared hosting, a small VPS, a container, or the folder you upload over FTP at midnight. If your host runs PHP 8, it runs RivetPHP.

## What you get

- A regex router with named path parameters, optional segments, and several HTTP methods per route
- Middleware that runs per route and can pass data forward through request attributes
- A request object built from the superglobals, with the body already decoded according to `Content-Type`
- Header access that behaves the same on every SAPI, including the ones that ignore `getallheaders()`
- JSON and raw responses with explicit status codes
- Exception to response mapping, so errors come out in whatever shape your API needs
- No dependencies beyond Composer's autoloader

## Install

RivetPHP is not on Packagist yet, so Composer has to be pointed at the repository first. Add this to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/kutayertugates/rivetphp"
        }
    ]
}
```

Then pull it in:

```bash
composer require kutayertugates/rivetphp:dev-master
```

PHP 8.0 or newer is required.

## Hello, world

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Rivetphp\Http\Request\Request;
use Rivetphp\Http\Response\JsonResponse;
use Rivetphp\Rivetphp;

$app = new Rivetphp();

$app->route('/', function (Request $request) {
    return new JsonResponse([
        'message' => 'Hello from RivetPHP',
        'time' => date('c'),
    ]);
});

$app->run();
```

Send a request to `/` and JSON comes back. The front controller that receives all requests is yours to prepare, since the framework takes the request as it arrives.

## Routing

A route is a path plus a handler. Handlers receive the request and return a response.

```php
$app->route('/about', function (Request $request) {
    return new JsonResponse(['page' => 'about']);
});
```

Segments wrapped in braces become named parameters, and `getParam()` reads them back:

```php
$app->route('/users/{id}', function (Request $request) {
    return new JsonResponse([
        'user_id' => $request->getParam('id'),
    ]);
});
```

Add a question mark to make the segment optional. It will be `null` when missing:

```php
$app->route('/blog/{slug?}', function (Request $request) {
    return new JsonResponse([
        'slug' => $request->getParam('slug', 'all-posts'),
    ]);
});
```

By default a route answers `GET`. Pass an array of methods as the third argument to widen that:

```php
$app->route('/users', $createUser, ['POST']);
$app->route('/users/{id}', $updateUser, ['PUT', 'PATCH']);
```

Route patterns are anchored, and a trailing slash is accepted either way. First match wins, so declare specific routes before catch-all ones.

## Reading the request

The request object carries the method, the path, the query string, cookies, files, headers, and the body:

```php
$request->method;   // "POST"
$request->path;     // "/users/42"
$request->query;    // $_GET
$request->body;     // decoded body
$request->headers;  // normalized header map
```

`body` is decoded by content type. JSON arrives as an associative array, form posts arrive the way `$_POST` does, and anything else stays a string. The untouched payload is always available as `$request->rawBody`.

Headers are normalized to lowercase with underscores, so `Content-Type` is read like this:

```php
$contentType = $request->getHeader('content_type', 'text/plain');
$token = $request->getHeader('authorization');
```

## Responses

`JsonResponse` sets the content type and encodes the payload. The second argument is the status code, `200` when omitted:

```php
return new JsonResponse(['created' => true], 201);
```

`BaseResponse` sends a raw body untouched, which is handy for HTML or plain text:

```php
return new BaseResponse('<h1>Maintenance</h1>', 503);
```

Every handler returns one of these, and `run()` takes care of sending it.

## Middleware

Middleware is a class extending `BaseMiddleware` with an `apply()` method. The constructor already receives the request, so you can inspect it, reject it, or attach data for the handler:

```php
<?php

use Rivetphp\Exceptions\Http\UnauthorizedException;
use Rivetphp\Middleware\BaseMiddleware;

class RequireToken extends BaseMiddleware
{
    public function apply()
    {
        $token = $this->request->getHeader('authorization');

        if ($token === null) {
            throw new UnauthorizedException('Missing token');
        }

        $this->request->setAttirbute('token', $token);
    }
}
```

Attach it as the fourth argument to `route()`:

```php
$app->route('/me', function (Request $request) {
    return new JsonResponse(['token' => $request->getAttirbute('token')]);
}, ['GET'], [RequireToken::class]);
```

Middlewares run in the order they are listed, before the handler. Attributes are the simplest way to hand something from middleware to your route, and they work because the same request instance travels through both.

## Errors

Anything thrown during dispatch can be turned into a response. Register a handler for the exception type and return whatever body makes sense for the endpoint:

```php
use Rivetphp\Exceptions\Http\UnauthorizedException;

$app->register_error_handler(UnauthorizedException::class, function (UnauthorizedException $e) {
    return new JsonResponse([
        'status' => 'error',
        'message' => $e->getMessage(),
    ], 401);
});
```

Handlers are checked in the order they were registered, and the first match wins, so a handler for `Exception` should come last. Each handler has to return a response object.

The framework ships with exceptions for the usual status codes: `BadRequestException` (400), `UnauthorizedException` (401), `PaymentRequiredException` (402), `ForbiddenExceiption` (403), `NotFoundException` (404), `MethodNotAllowedException` (405) and `RequestTimeoutException` (408). They are ordinary exceptions, so you can use them in middleware, in handlers, or wherever a request should stop.

## Where it stops

RivetPHP does not try to be everything. There is no ORM, no template engine, no queue, no CLI, no session manager. Database access, views and authentication are choices you make yourself, and the framework stays out of the way while you make them. What it does cover is the part almost every app needs on day one: turning an HTTP request into your code, and your return value back into a response.

If you need a full ecosystem, there are excellent frameworks for that. If you need to ship something small on hosting you do not control, this is aimed at you.

## License

MIT.
