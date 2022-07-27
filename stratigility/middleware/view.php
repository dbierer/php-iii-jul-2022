<?php
/**
 * View Middleware
 */
namespace stratigility\middleware;
use Laminas\Diactoros\Response;
return [
    // middleware: view log page; returns a response
    'view' => [
        'path' => '/view',
        'func' => function ($req, $handler) {
            $response = new Response();
            $contents = file_get_contents(LOG_FILE);
            $response->getBody()->write('<h1>View Access Log</h1><pre>' . $contents . '</pre>' . MENU);
            return $response;
        }
    ],
];

