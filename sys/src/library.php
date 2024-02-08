<?php

use Sys\I18n\I18n;
// use Az\Session\Session;
use Az\Validation\Csrf;
use Az\Route\RouteCollectionInterface;
use Az\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Yaml\Yaml;
use Sys\Config\Config;

function dd(...$values)
{
    ob_start();
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        echo 'file: ', $trace[0]['file'], ' line: ', $trace[0]['line'], '<br>';
        var_dump(...$values);
    $output = ob_get_clean();

    echo (php_sapi_name() !== 'cli') ? $output : str_replace('<br>', PHP_EOL, strip_tags($output, ['<br>', '<pre>']));
    exit;
}

function env(?string $path = null, $default = null): mixed
{
    static $env;

    if (!$env) {
        $env = Yaml::parseFile(APPPATH . '.env', Yaml::PARSE_CONSTANT);
    }

    return ($path) ? dot($env, $path, $default) : $env;
}

function container()
{
    global $container;
    return $container;
}

function config(string $file, ?string $path = null, $default = null, $cache = null)
{
    $config = container()->get(Config::class);
    $is_cache = $config->getEnabled();

    if (isset($cache)) {
        $config->enable($cache);
    }
    
    $result = $config->get($file, $path, $default);
    $config->enable($is_cache);
    return $result;
}

function dot(&$arr, $path, $default = null, $separator = '.') {
    $keys = explode($separator, $path);

    foreach ($keys as $key) {
        if (!is_array($arr) || !array_key_exists($key, $arr)) {
            $arr = &$default;
        } else {
            $arr = &$arr[$key];
        }       
    }

    return $arr;
}

function getMode()
{
    static $mode;

    if (isset($mode)) {
        return $mode;
    }

    if (PHP_SAPI === 'cli') {
        $mode = 'cli';
        return $mode;
    }

    $arrMode = config('mode', null, []);

    foreach ($arrMode as $key => $paths) {
        foreach ($paths as $path) {
            if (strpos($_SERVER['REQUEST_URI'], $path) === 0) {
                $mode = $key;
                return $mode;
            }
        }
    }

    $mode = 'web';
    return $mode;
}

function __(string $string, ?array $values = null): string
{
    $i18n = container()->get(I18n::class);
    return $i18n->gettext($string, $values);
}

function path($routeName, $params = [])
{
    $container = container();
    $routeCollection = $container->get(RouteCollectionInterface::class);
    $route = $routeCollection->getRoute($routeName);

    if (!array_key_exists('lang', $params) && $container->has(I18n::class)) {
        $i18n = $container->get(I18n::class);
        $params['lang'] = rtrim($i18n->langSegment(), '/');
    }

    return $route->path($params);
}

function url($routeName = null, $params = [])
{
    $request = container()->get(ServerRequestInterface::class);
    $scheme = getScheme($request);
    $host = $request->getServerParams()['SERVER_NAME'];

    $path = ($routeName) ? path($routeName, $params) : $request->getServerParams()['REQUEST_URI'];

    return $scheme . '://' . $host . $path;
}

function findPath($path, $all = false)
{
    $paths = glob(APPPATH . '*{\/src,}/' . ltrim($path, '/'), GLOB_BRACE);

    foreach ($paths as $path) {
        if (file_exists($path)) {
            if ($all) {
                $result[] = $path;
            } else {
                return $path;
            }
        }

        return $result ?? null;
    }
}

function json(?string $string)
{
    if (empty($string)) {
        return [];
    }

    return json_decode($string, true) ?? [];
}

function createCsrf()
{
    $salt = $_SERVER['HTTP_USER_AGENT'] ?? uniqid();
    $token = md5($salt.time().bin2hex(random_bytes(12)));
    $session = container()->get(SessionInterface::class);
    $session->flash('_csrf', $token);
    return $token;
}

function getScheme($request)
{
    $serverParams = $request->getServerParams();

    if (isset($serverParams['HTTPS'])) {
        $scheme = $serverParams['HTTPS'];
    } else {
        $scheme = '';
    }

    if (($scheme) && ($scheme != 'off')) {
        return'https';
    }
    else {
        return 'http';
    }
}

function getCallable(string|array $callable): mixed
{
    if (is_string($callable)) {
        if (str_contains($callable, '::')) {
            $callable = explode('::', $callable);
        } elseif (str_contains($callable, '@')) {
            $callable = explode('@', $callable);
        } else {
            $callable = [$callable, '__invoke'];
        }
    }
    
    return $callable;
}

function call($callable, array $data = []) {
    $container = container();
    $callable = getCallable($callable);

    try {
        return $container->call($callable, $data);        
    } catch (\DI\Definition\Exception\InvalidDefinition|\Invoker\Exception\InvocationException $e) {
        [$class, $method] = $callable;
        $instance = (is_string($class)) ? $container->make($class, $data) : $class;
        return $container->call([$instance, $method], $data);
    }
}

function is_ajax(ServerRequestInterface $request)
{
    $key = 'x_requested_with';
    $header = $request->getHeaderLine($key);

    if (empty($header)) {
        $header = $request->getHeaderLine('http_' . $key);
    }

    if (empty($header)) {
        $header = $request->getHeaderLine(strtoupper($key));
    }

    if (empty($header)) {
        $header = $request->getHeaderLine(strtoupper('http_' . $key));
    }

    if (empty($header)) {
        return false;
    }

    return true;
}

function render($file, $data)
{
    extract($data, EXTR_SKIP);               
    ob_start();
    include $file;
    return ob_get_clean();
}
