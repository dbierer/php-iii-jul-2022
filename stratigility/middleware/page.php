<?php
/**
 * Page Middleware
 */
namespace stratigility\middleware;
use Laminas\Diactoros\Response;
return [
    // middleware: page 1 and 2; returns a response
    'page' => [
        'path' => '/page',
        'func' => function ($req, $handler) {
            $path = $req->getUri()->getPath();
            $page = preg_replace('/[^0-9]/', '', $path);
            $response = new Response();
            $response->getBody()->write('<h1>Page ' . $page . '</h1>' . MENU);
            return $response;
        }
    ],
];

