<?php

namespace Sys\Controller;

use Az\Route\Route;
use Sys\FileResponse;
use Sys\Helper\ResponseType;
use Sys\Exception\ExceptionResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Media implements MiddlewareInterface
{
    private ExceptionResponseFactory $factory;

    public function __construct(ExceptionResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $params = $request->getAttribute(Route::class)->getParameters();

        $file = $params['file'];
        $lifetime = $params['lifetime'] ?? 0;

        if (!is_file($file)) {
            $file = WRITABLE . $file;
        }

        if (!is_file($file)) {
            $file = SYSPATH . 'vendor/az/sys/src/' . $file;
        }

        if (!is_file($file)) {
            $file = APPPATH . $file;
        }

        if (!is_file($file)) {
            return $this->factory->createResponse(ResponseType::html, 404, 'File not found');
        }

        return new FileResponse($file, (integer) $lifetime);
    }
}
