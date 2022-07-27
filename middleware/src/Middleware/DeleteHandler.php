<?php
declare(strict_types=1);
namespace Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
class DeleteHandler implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler) : ResponseInterface
    {
        $method = $request->getMethod();
        if (strtoupper($method) !== 'DELETE') return $handler->handle($request);
        $id   = $request->getQueryParams()['id'] ?? 0;
        $msg  = [];
        if (DbService::remove($id, $msg)) {
            $code = 200;
            $resp = ['status' => 'success', 'message' => $msg];
        } else {
            $code = 400;
            $resp = ['status' => 'failed', 'message' => $msg];
        }
        return (new JsonResponse($resp))->withStatus($code);
    }
}
