<?php
declare(strict_types=1);
namespace Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
/**
 * This handler is designed to move the request down the pipe
 */
class NextHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $resp = ['status' => 'next'];
        return (new JsonResponse($resp))->withStatus(202);
    }
}
