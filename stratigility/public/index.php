<?php
// TO RUN THIS DEMO:
/*
 * 1. From this directory run this command: "php -S localhost:9999 -t public"
 * 2. From your browser enter this URL: "http://localhost:9999"
 */
require __DIR__ . '/../vendor/autoload.php';

// main classes and functions needed
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\Middleware\NotFoundHandler;
use Laminas\Stratigility\MiddlewarePipe;

// NOTE: these are *functions* that provide convenient wrappers:
//       "middleware()" produces middleware from anonymous functions
//       "path()" adds routing and requires that you call "middleware()" as 2nd argument
use function Laminas\Stratigility\middleware;
use function Laminas\Stratigility\path;

// Load the middleware
foreach(glob('../middleware/*.php') as $file){
    $middleware[] = require $file;
}

define('LOG_FILE', __DIR__ . '/../logs/access.log');
define('MENU',
    '<a href="/">Home Page</a><br>
    <a href="/page/1">Page 1</a><br>
    <a href="/page/2">Page 2</a><br>
    <a href="/view">View Log</a>'
);

// left to right priority order in which middleware pages should be attached to the pipe
$order = ['log','page','view','home'];

$middleware = array_merge($middleware[1], $middleware[2], $middleware[3], $middleware[0]);

// set up the pipeline and server
$pipeline = new MiddlewarePipe();

// attach middleware to the pipe in $order
// NOTE the use of a linked list: $order is linked to $middleware
foreach ($order as $key) {
    if (isset($middleware[$key]['path'])) {
        $pipeline->pipe(path($middleware[$key]['path'], middleware($middleware[$key]['func'])));
    } else {
        $pipeline->pipe(middleware($middleware[$key]['func']));
    }
}

// 404 handler
$pipeline->pipe(new NotFoundHandler(function () {
    return new Response();
}));

$server = new RequestHandlerRunner(
    $pipeline,
    new SapiEmitter(),
    static function () {
        return ServerRequestFactory::fromGlobals();
    },
    static function (\Throwable $e) {
        $response = (new ResponseFactory())->createResponse(500);
        $response->getBody()->write(sprintf(
            'An error occurred: %s',
            $e->getMessage
        ));
        return $response;
    }
);

$server->run();
