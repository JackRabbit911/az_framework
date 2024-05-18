<?php

namespace Az\Route;

final class RouteAttribute
{
    public static function setByAttribute(Route $route)
    {
        $handler = $route->getHandler();
        $reflect = new \ReflectionMethod($handler[0], $handler[1]);
        $attribute = $reflect->getAttributes($route::class)[0] ?? null;

        // if ($route->getName() === 'auth') {
            // dd($attribute);
        // }

        if (empty($attribute)) {
            return;
        }

        $arguments = $attribute->getArguments();

        foreach ($arguments as $method => $arg) {
            if (is_array($arg)) {
                call_user_func_array([$route, $method], $arg);
            } else {
                call_user_func([$route, $method], $arg);
            }
        }
    }
}
