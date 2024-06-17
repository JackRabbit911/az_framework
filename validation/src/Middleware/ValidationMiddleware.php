<?php

namespace Az\Validation\Middleware;

use Az\Validation\Validation;
use HttpSoft\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class ValidationMiddleware implements MiddlewareInterface
{
    protected Validation $validation;
    protected ?string $path = null;

    public function __construct(Validation $validation)
    {
        $this->validation = $validation;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->setPath();
        $path = rawurldecode(rtrim($request->getUri()->getPath(), '/'));

        if ($this->path && $this->path !== $path) {
            return $handler->handle($request);
        }
        
        $this->setRules($request);
        $data = ($request->getMethod() === 'GET') ? $request->getQueryParams() : $request->getParsedBody();

        $data = $this->validate($request, $data);

        $this->debug($request);

        $GLOBALS['request'] = $request;

        return ($data) ? $handler->handle($request
                ->withParsedBody($data)
                ->withAttribute('validation', $this->validation))
            : $this->errorHandler($request);
    }

    protected function setPath() {}

    protected function setRules(ServerRequestInterface $request) {}

    protected function modifyData($data) {
        return $data;
    }

    protected function validate(ServerRequestInterface $request, array $data): ?array
    {
        $files = $request->getUploadedFiles();

        if ($this->validation->check($data, $files)) {
            return $this->modifyData($data);
        }

        return null;
    }

    protected function errorHandler(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute('session');
        $session->flash('validation', $this->validation->getResponse());
        return new RedirectResponse($request->getServerParams()['HTTP_REFERER'], 302);
    }

    protected function debug(ServerRequestInterface $request) {}
}
