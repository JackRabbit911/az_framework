<?php

declare(strict_types=1);

namespace Az\Route\Middleware;

use Az\Route\NormalizeResponse;
use Az\Route\Route;
use Invoker\InvokerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;

final class HandlerWrapperMiddleware implements MiddlewareInterface
{
    use NormalizeResponse;

    private ContainerInterface|InvokerInterface $container;
    private $handler;

    public function __construct(ContainerInterface|InvokerInterface $container, mixed $handler)
    {
        $this->container = $container;
        $this->handler = $handler;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute((Route::class));
        $params = $route->getParameters();
        $response = $this->container->call($this->handler, $params);
        return $this->normalizeResponse($request, $response);
    }
}
