<?php

namespace Sys\Template;

class App
{
    private array $objects = [];

    public function __call($name, $arguments)
    {
        return $this->objects[$name] ?? null;
    }

    public function add($key, $obj)
    {
        $this->objects[$key] = $obj;
    }

    public function cmp($class, array $attributes = [])
    {
        $class = ucfirst($class);

        foreach(glob(APPPATH . '**/Component/*.php') as $file) {
            if ($class === pathinfo($file, PATHINFO_FILENAME)) {
                $file = str_replace(APPPATH, '', $file);
                $arr_file = array_map(function ($f) {
                    return ucfirst(pathinfo($f, PATHINFO_FILENAME));
                }, explode('/', $file));

                $class = implode('\\', $arr_file);

                return container()->make($class, $attributes);
            }            
        }

        return null;
    }
}
