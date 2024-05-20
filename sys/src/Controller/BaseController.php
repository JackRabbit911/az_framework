<?php

namespace Sys\Controller;

use Az\Route\NormalizeResponse;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Az\Route\Route;

abstract class BaseController implements MiddlewareInterface
{
    use InvokeTrait;
    use NormalizeResponse;

    protected ServerRequestInterface $request;
    private string $action;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->request = $request;
        $route = $request->getAttribute(Route::class);
        $action = $route->getHandler()[1] 
        ?? $request->getAttribute('action') 
        ?? $route->getParameters()['action'] ?? '__invoke';

        $this->_before();
        $response = $this->call($action, $request->getAttribute(Route::class)->getParameters());
        $response = $this->normalizeResponse($request, $response);
        $this->_after($response);
        return $response;
    }

    protected function _before() {}

    protected function _after(&$response) {}
}
