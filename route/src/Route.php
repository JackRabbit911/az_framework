<?php

namespace Az\Route;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Az\Route\RouteCollection;

final class Route implements RouteInterface
{
    /**
     * @var string unique route name.
     */
    private string $name;

    /**
     * @var string path pattern with parameters.
     */
    private string $pattern;

    /**
     * @var mixed action, controller, callable, closure, etc.
     */
    private $handler;

    /**
     * @var string[] allowed request methods of the route.
     */
    private array $methods = [];

    /**
     * @var array<string, string|null> parameter names and regexp tokens.
     */
    private array $tokens = [];

    /**
     * @var array<string, string> parameter names and default parameter values.
     */
    private array $defaults = [];

    /**
     * @var string|null hostname or host regexp.
     */
    private ?string $host = null;

    /**
     * @var array<string, string> matched parameter names and matched parameter values.
     */
    private array $parameters = [];

    private array $filters = [];

    private ?bool $ajax = null;

    private array $pipeline = [];

    private string $groupPrefix = '';

    private RouteMatch $matcher;

     /**
     * @param string $name unique route name.
     * @param string $pattern path pattern with parameters.
     * @param mixed $handler action, controller, callable, closure, etc.
     * @param array $methods allowed request methods of the route.
     * @psalm-suppress MixedAssignment
     */
    public function __construct(string $pattern, $handler, $name = null)
    {
        if ($name) {
            $this->name = $name;
        }
        
        $this->pattern = $pattern;
        $this->handler = $handler;
    }

    public function methods(...$methods)
    {
        $this->methods = array_map(function ($v) {
            return strtoupper($v);
        }, $methods);

        return $this;
    }

    /**
     * Gets the unique route name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?? '';
    }

    /**
     * Gets the route handler.
     *
     * @return mixed
     */
    public function getHandler(): mixed
    {
        return $this->handler;
    }

    /**
     * Gets the matched parameters as `parameter names` => `parameter value`.
     *
     * The matched parameters appear may after successful execution of the `match()` method.
     *
     * @return array<string, string>
     * @see match()
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

     /**
     * Gets the default parameter values, as `parameter names` => `default values`.
     *
     * @return array<string, string>
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * Gets the allowed request methods of the route.
     *
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

     /**
     * Gets the parameter tokens, as `parameter names` => `regexp tokens`.
     *
     * @return array<string, string|null>
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    public function getPattern(): string
    {
        return (empty($this->pattern)) ? '/' : $this->pattern;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Adds the parameter regexp.
     *
     * @param array<string, mixed> $tokens `parameter names` => `regexp tokens`
     * @return self
     */
    public function tokens(array $tokens): self
    {
        foreach ($tokens as $key => $token) {
            if ($token === null) {
                $this->tokens[$key] = null;
                continue;
            }

            $this->tokens[$key] = $token;
        }

        return $this;
    }

    /**
     * Adds the default parameter values.
     *
     * @param array<string, mixed> $defaults `parameter names` => `default values`
     * @return self
     * @throws InvalidRouteParameterException if the default parameter value is not scalar.
     * @psalm-suppress MixedAssignment
     */
    public function defaults(array $defaults): self
    {
        foreach ($defaults as $key => $default) {
            if (!is_scalar($default)) {
                // throw InvalidRouteParameterException::forDefaults($default);
            }

            $this->defaults[$key] = (string) $default;
        }

        return $this;
    }

    /**
     * Sets the route host.
     *
     * @param string $host hostname or host regexp.
     * @return self
     */
    public function host(string $host): self
    {
        $this->host = trim($host, '/');
        return $this;
    }

    public function filter(callable $filter): self
    {
        $this->filters[] = $filter;
        return $this;
    }

    public function ajax(?bool $value = null)
    {
        $this->ajax = $value;
    }

    public function middleware(...$params): self
    {
        return $this->pipe($params);
    }

    public function pipe(...$params): self
    {
        foreach ($params as $middleware) {
            if (is_array($middleware)) {
                $this->pipeline += $middleware;
            } else {
                $this->pipeline[] = $middleware;
            }
        }

        return $this;
    }

    public function unPipe(...$params)
    {
        foreach ($params as $middleware) {
            $key = array_search($middleware, $this->pipeline);

            if ($key) {
                unset($this->pipeline[$key]);
            }
        }

        return $this;
    }

    public function getPipeline()
    {
        return $this->pipeline;
    }

    public function groupPrefix(string $prefix): self
    {
        $this->groupPrefix = $prefix;
        return $this;
    }

    public function getGroupPrefix()
    {
        return $this->groupPrefix;
    }

    public function getSantizePrefix(ServerRequestInterface $request)
    {
        if (empty($this->groupPrefix) || strpos($this->groupPrefix, '{') === false) {
            return $this->groupPrefix;
        }

        return $this->matcher->parsePrefix($request);
    }

    /**
     * Checks whether the request URI matches the current route.
     *
     * If there is a match and the route has matched parameters, they will
     * be saved and available via the `Route::getParameters()` method.
     *
     * @param ServerRequestInterface $request
     * @return bool whether the route matches the request URI.
     */
    public function match(ServerRequestInterface $request): bool
    {
        $this->matcher = new RouteMatch($this);
        $params = $this->matcher->parse($request, $this->pattern);

        if ($params === false) {
            return false;
        }

        $this->parameters = array_filter($params) + $this->defaults;

        if (is_string($this->handler)) {
            if (str_contains($this->handler, '::')) {
                $this->handler = explode('::', $this->handler);
            } elseif (str_contains($this->handler, '@')) {
                $this->handler = explode('@', $this->handler);
            } else {
                $this->handler = [$this->handler, $this->parameters['action'] ?? '__invoke'];
                // unset($this->parameters['action']);
            }
        }

        if (is_array($this->handler)) {
            if (!method_exists($this->handler[0], $this->handler[1])) {
                return false;
            }

            RouteAttribute::setByAttribute($this);
        }

        if (!$this->checkHost($request) || !$this->checkTokens() || !$this->checkAjax($request)
            || !$this->checkFilters($request) || !$this->checkMethod($request)) {
            return false;
        }

        return true;
    }

    public function path(array $params = []): string
    {
        return (new RouteMatch($this))->path($params);
    }

    private function checkMethod(ServerRequestInterface $request)
    {
        if (!empty($this->methods) 
            && !in_array(strtoupper($request->getMethod()), $this->methods, true)) {
            $methods = implode(', ', array_unique(array_filter($this->methods)));
            RouteCollection::$methodNotAllowed = $methods;
            return false;
        } 

        return true;
    }

    private function checkHost(ServerRequestInterface $request)
    {
        if ($this->host && !preg_match('~^' 
                . str_replace('.', '\\.', $this->host) 
                . '$~i', $request->getUri()->getHost())) {
            return false;
        }

        return true;
    }

    private function checkTokens()
    {
        foreach ($this->tokens as $key => $pattern) {
            if (array_key_exists($key, $this->parameters) 
                && !preg_match('~^(' . $pattern . ')$~i', $this->parameters[$key])) {
                return false;
            }
        }

        return true;
    }

    private function checkFilters(ServerRequestInterface $request)
    {
        foreach ($this->filters as $filter) {
            if (!$filter($this, $request)) {
                return false;
            }
        }

        return true;
    }

    private function checkAjax(ServerRequestInterface $request)
    {
        if ($this->ajax === null) {
            return true;
        }

        return is_ajax($request) === $this->ajax;        
    }
}
