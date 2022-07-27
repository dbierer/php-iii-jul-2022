<?php
/**
 * Home Middleware
 */
namespace stratigility\middleware;
use Laminas\Diactoros\Response;
return [
    // middleware: home page; returns a response
    'home' => [
        'path' => FALSE,
        'func' => function ($req, $handler) {
            if (! in_array($req->getUri()->getPath(), ['/', ''], true)) {
                return $handler->handle($req);
            }
            $response = new Response();
            $response->getBody()->write('<h1>Home Page</h1>' . MENU);
            return $response;
        }
    ]
];

