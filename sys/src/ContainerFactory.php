<?php

namespace Sys;

use DI\Container;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

final class ContainerFactory
{
    private $container;
    private string $mode;

    public function __construct($mode = 'web')
    {
        $this->mode = $mode;
        // $this->mode = getMode();
    }

    public function create(ContainerBuilder $builder): Container
    {
        if ($this->container instanceof ContainerInterface) {
            return $this->container;
        }

        $builder->useAttributes(true);

        $path = findPath('config/container/');

        if (is_file(($common = $path . 'common.php'))) {
            $builder->addDefinitions(require_once $common);
        }

        if (is_file(($mode = $path . $this->mode . '.php'))) {
            $builder->addDefinitions(require_once $mode);
        }

        return $builder->build();
    }
}
