<?php
// TO RUN THIS DEMO:
/*
 * cd ..
 * php -S localhost:9999 -t public index.php
 *
 */
// TO TEST:
/*
*
GET all:
curl -X GET http://localhost:9999
*
GET single order:
curl -X GET http://localhost:9999?id=INT
*
INSERT:
curl -X POST \
  -F status=all|open|cancelled|held|invoiced|complete \
  -F amount=FLOAT \
  -F description=STRING \
  -F customer=INT \
  http://localhost:9999
*
DELETE:
curl -X DELETE http://localhost:9999?id=INT
*
*/

define('LOG_FILE', __DIR__ . '/../logs/access.log');
define('DB_CONFIG', ['dbname' => 'phpcourse', 'dbuser' => 'vagrant', 'dbpwd' => 'vagrant']);

require __DIR__ . '/../vendor/autoload.php';

// main classes and functions needed
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Response\JsonResponse;
use Middleware\{
    Logger,
    ListHandler,
    InsertHandler,
    DeleteHandler,
    NextHandler,
};

// build the pipe
$pipe = [
    Logger::class => NextHandler::class,
    DeleteHandler::class => NextHandler::class,
    InsertHandler::class => NextHandler::class,
    ListHandler::class => NULL,
];

// build a PSR-7 Request object
$request  = ServerRequestFactory::fromGlobals();

// run the pipe
foreach ($pipe as $key => $val) {
    $middleware = new $key();
    $handler    = (!empty($val)) ? new $val() : NULL;
    if (method_exists($middleware, 'process')) {
        $response = $middleware->process($request, $handler);
    } else {
        $response = $middleware->handle($request);
    }
    // check response: is it time to stop?
    $code = $response->getStatusCode();
    if ($code !== 202) break;
}
echo $response->getBody();
