<?php

namespace Sys;

use Az\Route\Route;
use Psr\Http\Message\ServerRequestInterface;

class SimpleRequest
{
    private ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = &$request;
    }

    public function __call($name, $arguments)
    {
        return $this->request->getAttribute($name);
    }

    public function uri(): string
    {
        return $this->request->getUri()->getPath();
    }

    public function url()
    {
        return (string) $this->request->getUri();
    }

    public function method()
    {
        return $this->request->getMethod();
    }

    public function host(): string
    {
        return $this->request->getUri()->getHost();
    }

    public function query()
    {
        return $this->request->getUri()->getQuery();
    }

    public function get()
    {
        return $this->request->getQueryParams();
    }

    public function scheme(): string
    {
        return $this->request->getUri()->getScheme();
    }

    public function post()
    {
        return $this->request->getParsedBody();
    }

    public function body()
    {
        return (string) $this->request->getBody();
    }

    public function ajax()
    {
        $key = 'x_requested_with';
        $header = $this->request->getHeaderLine($key);

        if (empty($header)) {
            $header = $this->request->getHeaderLine('http_' . $key);
        }

        if (empty($header)) {
            $header = $this->request->getHeaderLine(strtoupper($key));
        }

        if (empty($header)) {
            $header = $this->request->getHeaderLine(strtoupper('http_' . $key));
        }

        if (empty($header)) {
            return false;
        }

        return true;
    }

    public function header($key)
    {
        return $this->request->getHeaderLine($key);
    }

    public function is_active($path, $strong = true)
    {
        $path = trim($path, '/');
        $uri = trim($this->request->getUri()->getPath(), '/');

        return ($strong) ? $uri === $path : str_starts_with($uri, $path);
    }

    public function route()
    {
        return $this->request->getAttribute(Route::class);
    }
}
