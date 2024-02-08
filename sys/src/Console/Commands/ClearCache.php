<?php

namespace Sys\Console\Commands;

use Sys\Config\Cache as CacheConfig;
use Sys\Console\Command;

final class ClearCache extends Command
{
    protected function configure()
    {
        $this->addArgument('func', 'Which cache will we clear?');
    }

    public function execute($func)
    {
        if (!method_exists($this, $func)) {
            $this->climate->red()->inline('WARNING! ');
            $this->climate->out("Argument <yellow>'$func'</yellow> not recognized");
            exit;
        }

        call([__CLASS__, $func]);
        $this->climate->out("Cache '$func' was cleared");
    }

    public function config(CacheConfig $cacheConfig)
    {
        $cacheConfig->clearCacheFile();
    }
}