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

    public function queryParams($key = null)
    {
        return ($key) ? $this->request->getQueryParams()[$key] ?? null : $this->request->getQueryParams();
    }

    public function addQuery(array|string $param)
    {
        $uri = $this->request->getUri();
        $path = $uri->getPath();
        $query = $uri->getQuery() ?? '';

        parse_str($query, $result);

        if (is_string($param)) {
            parse_str($param, $param);
        }

        $result = array_merge($result, $param);
        $query_str = http_build_query($result);

        return (!empty($query_str)) ? $path . '?' . $query_str : $path;
    }

    public function rmQuery(string $key)
    {
        $uri = $this->request->getUri();
        $path = $uri->getPath();
        $query = $uri->getQuery() ?? '';

        parse_str($query, $result);

        unset($result[$key]);
        $query_str = http_build_query($result);

        return (!empty($query_str)) ? $path . '?' . $query_str : $path;
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

        return ($strong) ? $uri === $path : explode('/', $uri)[0] === explode('/', $path)[0];
    }

    public function route()
    {
        return $this->request->getAttribute(Route::class);
    }

    public function serverParams(?string $key = null)
    {
        return ($key) ? $this->request->getServerParams()[$key] : $this->request->getServerParams();
    }

    public function referer($default = '/')
    {
        if (isset($this->request->getServerParams()['HTTP_REFERER'])) {
            return $this->request->getServerParams()['HTTP_REFERER'];
        }

        return $default ?? path('home');
    }
}
