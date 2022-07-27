<?php
declare(strict_types=1);
namespace Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Template\TemplateRendererInterface;
class ListHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $id   = $request->getQueryParams()['id'] ?? 0;
        $data = ['status' => 'success', 'data' => DbService::getList((int) $id)];
        return (new JsonResponse($data))->withStatus(200);
    }
}
