<?php

namespace Tests\Az\Route\Deps;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Middleware implements MiddlewareInterface
{
    private string $str;

    public function __construct(string $str)
    {
        $this->str = $str;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $str = $request->getAttribute('str', '');
        $str .= $this->str;

        return $handler->handle($request->withAttribute('str', $str));
    }
}
